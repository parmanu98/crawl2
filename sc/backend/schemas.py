from pydantic import BaseModel
from typing import List

class CrawlRequest(BaseModel):
    seed_url: str
    depth: int
    same_domain: bool
    fields: List[str]
