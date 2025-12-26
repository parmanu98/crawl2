from fastapi import FastAPI, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from backend.schemas import CrawlRequest
from backend.crawler_runner import run_crawler, get_status
import uuid

app = FastAPI(title="Universal Web Crawler")

# ✅ CORS FIX
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.post("/crawl")
def start_crawl(req: CrawlRequest, bg: BackgroundTasks):
    job_id = str(uuid.uuid4())
    bg.add_task(run_crawler, job_id, req)
    return {"job_id": job_id}

@app.get("/status/{job_id}")
def status(job_id: str):
    return get_status(job_id)
