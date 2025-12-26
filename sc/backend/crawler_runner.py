import subprocess
import json
import os


STATUS = {}

def run_crawler(job_id: str, req):
    STATUS[job_id] = {"status": "running"}

    try:
        # Create absolute output file path
        output_file = os.path.abspath(f"backend/storage/{job_id}.json")
        
        # Run the actual Scrapy crawler
        cmd = [
            "scrapy", "crawl", "universal",
            "-a", f"start_urls={req.seed_url}",
            "-a", f"depth={req.depth}",
            "-a", f"same_domain={req.same_domain}",
            "-a", f"fields={','.join(req.fields)}",
            "-a", f"output_file={output_file}",
            # Remove -o flag to avoid conflicts with pipeline
        ]

        print(f"Running command: {' '.join(cmd)}")
        print(f"Output file: {output_file}")

        # Change to crawler directory and run
        result = subprocess.run(cmd, cwd="crawler", capture_output=True, text=True)
        
        print(f"Return code: {result.returncode}")
        print(f"STDOUT: {result.stdout}")
        print(f"STDERR: {result.stderr}")
        
        if result.returncode == 0:
            # Read the results from the output file
            if os.path.exists(output_file):
                with open(output_file, 'r', encoding='utf-8') as f:
                    try:
                        results = json.load(f)
                        STATUS[job_id] = {
                            "status": "completed",
                            "result": results
                        }
                        print(f"Successfully loaded {len(results)} items from {output_file}")
                    except json.JSONDecodeError as e:
                        STATUS[job_id] = {
                            "status": "completed",
                            "result": [],
                            "message": f"JSON decode error: {str(e)}"
                        }
            else:
                STATUS[job_id] = {
                    "status": "completed", 
                    "result": [],
                    "message": f"No output file generated at {output_file}"
                }
        else:
            STATUS[job_id] = {
                "status": "failed",
                "error": f"Scrapy failed (code {result.returncode}): {result.stderr}"
            }
            
    except Exception as e:
        STATUS[job_id] = {
            "status": "failed",
            "error": f"Exception: {str(e)}"
        }

def get_status(job_id: str):
    return STATUS.get(job_id, {"status": "not_found"})


# JOB_STATUS = {}

# def run_crawler(job_id, req):
#     JOB_STATUS[job_id] = "running"

#     cmd = [
#         "scrapy", "crawl", "universal",
#         "-a", f"start_urls={req.seed_url}",
#         "-a", f"depth={req.depth}",
#         "-a", f"same_domain={req.same_domain}",
#         "-a", f"fields={','.join(req.fields)}",
#         "-o", f"backend/storage/{job_id}.json"
#     ]

#     subprocess.run(cmd)
#     JOB_STATUS[job_id] = "completed"

# def get_status(job_id):
#     return JOB_STATUS.get(job_id, "unknown")
