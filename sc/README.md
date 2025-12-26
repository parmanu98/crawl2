# Universal Web Crawler

A powerful, full-stack web scraping application that allows users to crawl websites and extract structured data with an intuitive professional UI. Built with FastAPI, Scrapy, and Playwright for modern web scraping capabilities.

## 🎯 Features

### **Core Crawling Capabilities**
- **Universal Spider**: Configurable web crawler that adapts to different websites
- **Recursive Crawling**: Follow links up to specified depth with domain filtering
- **JavaScript Rendering**: Uses Playwright/Chromium for JavaScript-heavy sites (Google Travel, dynamic content)
- **Selective Data Extraction**: Choose exactly what to extract from pages
- **Rate Limiting**: Built-in throttling (1-10s delay) and concurrent request limits (8 max)
- **Asynchronous Processing**: Non-blocking background job execution

### **Data Extraction Options**
- **Page Title**: Extract page titles
- **Meta Data**: Description, keywords, and other meta tags
- **Text Content**: Extract paragraph content and text
- **Links**: Find all external and internal links
- **Images**: Extract image URLs
- **Structured Data**: JSON-LD, prices, ratings, travel info, products, contact details, dates
- **HTML Source**: Complete raw HTML source code (like view-source:)

### **Advanced Features**
- **Structured Data Analysis**: Automatically extract JSON-LD, prices, ratings, and more
- **HTML Source Extraction**: Capture complete website structure including:
  - Raw HTML source code
  - Document structure analysis
  - All scripts (inline and external)
  - All stylesheets and CSS
  - Meta tags and attributes
  - Form structures
  - Data attributes
  - HTML comments
- **Download HTML**: Export complete HTML source as .html file
- **Professional UI**: Modern, responsive interface with syntax-highlighted JSON output
- **Copy to Clipboard**: One-click JSON copying
- **Real-time Status**: Live job status updates with polling

### **Technical Features**
- **Background Jobs**: Asynchronous crawling with job tracking
- **Error Handling**: Comprehensive error reporting and recovery
- **CORS Support**: Frontend-backend communication enabled
- **Browser Automation**: Playwright integration for JavaScript rendering
- **Flexible Configuration**: Customizable crawl depth, domain restrictions, and extraction fields

## 📋 Technology Stack

### **Backend**
- **FastAPI**: Modern async web framework
- **Uvicorn**: ASGI server
- **Pydantic**: Data validation
- **Scrapy**: Web scraping framework
- **Scrapy-Playwright**: Browser automation for Scrapy
- **BeautifulSoup4**: HTML parsing
- **lxml**: XML/HTML processing

### **Frontend**
- **HTML5**: Semantic markup
- **CSS3**: Modern styling with gradients and animations
- **Vanilla JavaScript**: No framework dependencies
- **Font Awesome**: Icon library

### **Infrastructure**
- **Python 3.14+**: Latest Python version
- **Chromium**: Browser engine via Playwright
- **Twisted**: Async networking for Scrapy

## 🚀 Quick Start

### **Prerequisites**
- Python 3.14 or higher
- pip (Python package manager)
- Git (optional, for cloning)

### **Step 1: Clone or Download the Project**

```bash
# Using Git
git clone <repository-url>
cd universal-web-crawler

# Or download and extract the ZIP file
```

### **Step 2: Create Virtual Environment**

```bash
# Windows
python -m venv venv
venv\Scripts\activate

# macOS/Linux
python3 -m venv venv
source venv/bin/activate
```

### **Step 3: Install Dependencies**

```bash
pip install -r requirements.txt
```

### **Step 4: Install Playwright Browsers**

```bash
playwright install
```

This downloads Chromium browser for JavaScript rendering (required for dynamic sites like Google Travel).

### **Step 5: Start the Backend Server**

```bash
# From the project root directory
uvicorn backend.main:app --reload
```

You should see:
```
INFO:     Uvicorn running on http://127.0.0.1:8000
INFO:     Application startup complete
```

### **Step 6: Open the Frontend**

Open your browser and navigate to:
```
file:///path/to/project/frontend/index.html
```

Or use a local HTTP server:
```bash
# Python 3
python -m http.server 8001 --directory frontend

# Then open: http://localhost:8001
```

### **Step 7: Start Crawling!**

1. Enter a URL (e.g., `https://www.idmerit.com/`)
2. Set crawl depth (0-5)
3. Select data to extract
4. Click "Start Crawling"
5. View results in real-time

## 📖 Usage Guide

### **Basic Crawling**

1. **Enter URL**: Paste the website URL you want to crawl
2. **Set Depth**: 
   - `0` = Only the main page
   - `1` = Main page + linked pages
   - `2+` = Deeper crawling (slower)
3. **Select Fields**:
   - **Title**: Page titles
   - **Meta**: Meta descriptions and keywords
   - **Content**: Text content from paragraphs
   - **Links**: All external links
   - **Images**: Image URLs
   - **Structured Data**: Prices, ratings, JSON-LD data
   - **HTML Source**: Complete HTML structure
4. **Domain Filter**: Check to stay within the same domain
5. **Start Crawling**: Click the button and wait for results

### **Advanced Features**

#### **Structured Data Extraction**
When you select "Structured Data", the crawler automatically extracts:
- **JSON-LD**: Structured data markup
- **Prices**: Currency amounts in various formats
- **Ratings**: Star ratings and review scores
- **Travel Info**: Hotels, locations, accommodations
- **Products**: Product names, descriptions, images
- **Contact**: Phone numbers and email addresses
- **Dates**: Various date formats

#### **HTML Source Download**
1. Check "HTML Source" checkbox
2. Start crawling
3. Click "Download HTML" button
4. Get complete HTML file for offline analysis

#### **Copy Results**
- Click "Copy JSON" to copy all results to clipboard
- Paste into any text editor or JSON viewer

### **Example URLs to Test**

```
# E-commerce
https://www.amazon.com/

# Travel
https://www.google.to/travel/search

# News
https://www.bbc.com/

# Documentation
https://docs.python.org/

# Blog
https://medium.com/
```

## 📁 Project Structure

```
universal-web-crawler/
├── backend/
│   ├── main.py                 # FastAPI application
│   ├── schemas.py              # Pydantic models
│   ├── crawler_runner.py        # Crawler execution logic
│   └── storage/                # Crawled data storage
├── crawler/
│   ├── crawler/
│   │   ├── spiders/
│   │   │   ├── universal_spider.py    # Main spider
│   │   │   └── test_spider.py         # Test spider
│   │   ├── extractor.py        # Data extraction logic
│   │   ├── pipelines.py         # Data processing
│   │   ├── settings.py          # Scrapy configuration
│   │   └── middlewares.py       # Scrapy middlewares
│   └── scrapy.cfg              # Scrapy project config
├── frontend/
│   ├── index.html              # Main UI
│   └── app.js                  # Frontend logic
├── requirements.txt            # Python dependencies
├── .gitignore                  # Git ignore rules
└── README.md                   # This file
```

## 🔧 Configuration

### **Scrapy Settings** (`crawler/crawler/settings.py`)

```python
ROBOTSTXT_OBEY = False              # Respect robots.txt
DOWNLOAD_DELAY = 2                  # Delay between requests (seconds)
CONCURRENT_REQUESTS = 4             # Max concurrent requests
DOWNLOAD_TIMEOUT = 30               # Request timeout (seconds)
RETRY_TIMES = 3                     # Retry failed requests
```

### **Backend Settings** (`backend/main.py`)

- CORS enabled for all origins
- Background task processing
- Job status tracking

## 🐛 Troubleshooting

### **Issue: "Playwright browsers not found"**
```bash
playwright install
```

### **Issue: "Port 8000 already in use"**
```bash
# Use a different port
uvicorn backend.main:app --reload --port 8001
```

### **Issue: "CORS error in browser"**
- Ensure backend is running on `http://localhost:8000`
- Check browser console for specific error messages

### **Issue: "No data extracted"**
- Try with depth=0 first
- Check if the website blocks scrapers
- Try enabling "HTML Source" to see if page is being fetched

### **Issue: "Crawl takes too long"**
- Reduce crawl depth
- Uncheck unnecessary data fields
- Check network connection speed

## 📊 API Endpoints

### **POST /crawl**
Start a new crawl job

**Request:**
```json
{
  "seed_url": "https://example.com",
  "depth": 1,
  "same_domain": true,
  "fields": ["title", "meta", "content", "links"]
}
```

**Response:**
```json
{
  "job_id": "uuid-string"
}
```

### **GET /status/{job_id}**
Check job status

**Response:**
```json
{
  "status": "completed",
  "result": [
    {
      "url": "https://example.com",
      "title": "Page Title",
      "meta": {...},
      "content": "...",
      "links": [...],
      "structured_data": {...},
      "html_source": {...}
    }
  ]
}
```

## 🎨 UI Features

### **Professional Design**
- Modern gradient background
- Responsive layout (mobile & desktop)
- Smooth animations and transitions
- Syntax-highlighted JSON output
- Real-time status updates

### **User Experience**
- Loading spinners during crawling
- Status indicators (running, completed, failed)
- Error messages with helpful information
- One-click copy to clipboard
- HTML source download button
- Empty states with helpful guidance

## 🔐 Security Considerations

- **ROBOTSTXT_OBEY**: Set to `False` for testing, consider enabling for production
- **Rate Limiting**: Configured to be respectful to servers
- **User Agent**: Identifies as a legitimate browser
- **Timeout**: Prevents hanging requests
- **Error Handling**: Graceful failure recovery

## 📝 Requirements

See `requirements.txt` for complete list:
- scrapy==2.13.4
- scrapy-playwright==0.0.44
- fastapi==0.127.0
- uvicorn==0.40.0
- beautifulsoup4==4.14.3
- lxml==6.0.2
- pydantic==2.12.5
- playwright==1.57.0

## 🚀 Performance Tips

1. **Reduce Depth**: Lower crawl depth = faster results
2. **Limit Fields**: Only extract needed data
3. **Use Same Domain**: Reduces number of pages to crawl
4. **Increase Timeout**: For slow websites
5. **Adjust Delays**: Balance between speed and server load

## 📚 Learning Resources

- [Scrapy Documentation](https://docs.scrapy.org/)
- [FastAPI Documentation](https://fastapi.tiangolo.com/)
- [Playwright Documentation](https://playwright.dev/)
- [BeautifulSoup Documentation](https://www.crummy.com/software/BeautifulSoup/)

## 🤝 Contributing

Feel free to fork, modify, and improve this project!

## 📄 License

This project is open source and available under the MIT License.

## 💡 Future Enhancements

- [ ] Database integration for result storage
- [ ] User authentication and API keys
- [ ] Scheduled crawling jobs
- [ ] Export to CSV/Excel
- [ ] Advanced filtering and search
- [ ] Proxy support
- [ ] Custom extraction rules
- [ ] Webhook notifications
- [ ] Rate limiting per user
- [ ] Web-based job history

## 🆘 Support

For issues, questions, or suggestions:
1. Check the Troubleshooting section
2. Review the API documentation
3. Check browser console for errors
4. Verify all dependencies are installed

## 📞 Contact

For questions or feedback, please open an issue in the repository.

---

**Happy Crawling! 🕷️**

Built with ❤️ using FastAPI, Scrapy, and Playwright
