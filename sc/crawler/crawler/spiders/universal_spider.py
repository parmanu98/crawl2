import scrapy
from urllib.parse import urlparse
from datetime import datetime
from crawler.extractor import extract_data


class UniversalSpider(scrapy.Spider):
    name = "universal"

    def __init__(self, start_urls, depth="1", fields="title", same_domain="True", output_file=None, *args, **kwargs):
        super().__init__(*args, **kwargs)
        
        self.start_urls = start_urls.split(",")
        self.custom_settings = {"DEPTH_LIMIT": int(depth)}
        self.fields = fields.split(",")
        # Convert string to boolean
        self.same_domain = same_domain.lower() == "true"
        # Store output file for pipeline
        self.output_file = output_file or "output.json"

    def start_requests(self):
        for url in self.start_urls:
            # Enable Playwright for JavaScript-heavy sites like Google
            use_playwright = any(domain in url.lower() for domain in ['google.', 'youtube.', 'facebook.', 'twitter.'])
            
            yield scrapy.Request(
                url,
                meta={"playwright": True} if use_playwright else {},
                callback=self.parse,
                errback=self.handle_error
            )

    def handle_error(self, failure):
        print(f"Request failed: {failure}")

    def parse(self, response):
        try:
            data = extract_data(response.text, response.url, self.fields)
            data["domain"] = urlparse(response.url).netloc
            data["scraped_at"] = datetime.utcnow().isoformat()

            yield data

            # Only follow links if depth allows and same_domain is respected
            if int(self.custom_settings.get("DEPTH_LIMIT", 1)) > 0:
                for link in response.css("a::attr(href)").getall()[:5]:  # Limit to 5 links for testing
                    if link and link.startswith("http"):
                        if self.same_domain:
                            if urlparse(link).netloc != urlparse(response.url).netloc:
                                continue
                        yield response.follow(link, callback=self.parse, errback=self.handle_error)
                
        except Exception as e:
            print(f"Error in parse: {e}")
            yield {"url": response.url, "error": str(e)}
