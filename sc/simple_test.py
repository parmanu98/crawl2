#!/usr/bin/env python3

import subprocess
import os

def simple_test():
    print("=== Simple Scrapy Test ===")
    
    # Test with minimal parameters and shorter timeout
    cmd = [
        "scrapy", "crawl", "universal",
        "-a", "start_urls=https://httpbin.org/html",  # Simple test site
        "-a", "depth=0",  # No following links
        "-a", "same_domain=True",
        "-a", "fields=title,content",
        "-s", "LOG_LEVEL=INFO"  # Less verbose
    ]
    
    print(f"Command: {' '.join(cmd)}")
    
    try:
        result = subprocess.run(cmd, cwd="crawler", capture_output=True, text=True, timeout=30)
        
        print(f"Return code: {result.returncode}")
        print(f"STDOUT: {result.stdout}")
        print(f"STDERR: {result.stderr}")
        
        return result.returncode == 0
        
    except subprocess.TimeoutExpired:
        print("Command timed out after 30 seconds!")
        return False
    except Exception as e:
        print(f"Exception: {e}")
        return False

if __name__ == "__main__":
    simple_test()