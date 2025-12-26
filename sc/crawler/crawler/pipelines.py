import json
import os

class JsonPipeline:
    def open_spider(self, spider):
        # Get output file from spider settings or use default
        output_file = getattr(spider, 'output_file', 'output.json')
        
        # Ensure directory exists
        os.makedirs(os.path.dirname(output_file), exist_ok=True) if os.path.dirname(output_file) else None
        
        self.file = open(output_file, "w", encoding="utf-8")
        self.items = []
        self.output_file = output_file

    def close_spider(self, spider):
        json.dump(self.items, self.file, ensure_ascii=False, indent=2)
        self.file.close()
        print(f"JsonPipeline: Saved {len(self.items)} items to {self.output_file}")

    def process_item(self, item, spider):
        self.items.append(dict(item))
        return item
