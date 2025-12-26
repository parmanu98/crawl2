from bs4 import BeautifulSoup
import re
import json

def extract_data(html, url, fields):
    try:
        soup = BeautifulSoup(html, "lxml")

        # Store original HTML before cleaning
        original_html = html

        for tag in soup(["script", "style", "noscript"]):
            tag.decompose()

        data = {"url": url}

        if "title" in fields:
            title_tag = soup.find("title")
            data["title"] = title_tag.get_text().strip() if title_tag else ""

        if "meta" in fields:
            desc_meta = soup.find("meta", attrs={"name": "description"})
            keywords_meta = soup.find("meta", attrs={"name": "keywords"})
            
            data["meta"] = {
                "description": desc_meta.get("content", "") if desc_meta else "",
                "keywords": keywords_meta.get("content", "") if keywords_meta else ""
            }

        if "content" in fields:
            paragraphs = soup.find_all("p")
            data["content"] = " ".join(
                p.get_text(strip=True) for p in paragraphs
            )

        if "links" in fields:
            links = []
            for a in soup.find_all("a", href=True):
                href = a.get("href", "")
                if href.startswith("http"):
                    links.append(href)
            data["links"] = list(set(links))  # Remove duplicates

        if "images" in fields:
            images = []
            for img in soup.find_all("img", src=True):
                src = img.get("src", "")
                if src.startswith("http"):
                    images.append(src)
            data["images"] = list(set(images))  # Remove duplicates

        # Enhanced extraction for travel/e-commerce sites
        if "structured" in fields:
            data["structured_data"] = extract_structured_data(soup, original_html, url)

        # Raw HTML source extraction
        if "source" in fields:
            data["html_source"] = extract_html_source(original_html, url)

        return data
        
    except Exception as e:
        print(f"Error in extract_data: {e}")
        return {"url": url, "error": str(e)}

def extract_html_source(html, url):
    """Extract and analyze the complete HTML source structure"""
    source_data = {}
    
    try:
        # Store the complete raw HTML
        source_data["raw_html"] = html
        source_data["html_length"] = len(html)
        
        # Parse with BeautifulSoup to analyze structure
        soup = BeautifulSoup(html, "lxml")
        
        # Document structure analysis
        source_data["document_structure"] = analyze_document_structure(soup)
        
        # Extract all scripts (including inline JavaScript)
        source_data["scripts"] = extract_scripts(soup)
        
        # Extract all stylesheets and CSS
        source_data["styles"] = extract_styles(soup)
        
        # Extract meta tags
        source_data["meta_tags"] = extract_all_meta_tags(soup)
        
        # Extract head section
        head_tag = soup.find("head")
        if head_tag:
            source_data["head_html"] = str(head_tag)
        
        # Extract body attributes and structure
        body_tag = soup.find("body")
        if body_tag:
            source_data["body_attributes"] = dict(body_tag.attrs) if body_tag.attrs else {}
            source_data["body_classes"] = body_tag.get("class", [])
            source_data["body_id"] = body_tag.get("id", "")
        
        # Extract all HTML comments
        source_data["comments"] = extract_comments(html)
        
        # Extract DOCTYPE and HTML attributes
        source_data["doctype"] = extract_doctype(html)
        html_tag = soup.find("html")
        if html_tag:
            source_data["html_attributes"] = dict(html_tag.attrs) if html_tag.attrs else {}
        
        # Extract form structures
        source_data["forms"] = extract_forms(soup)
        
        # Extract all data attributes
        source_data["data_attributes"] = extract_data_attributes(soup)
        
    except Exception as e:
        source_data["extraction_error"] = str(e)
    
    return source_data

def analyze_document_structure(soup):
    """Analyze the overall structure of the HTML document"""
    structure = {}
    
    # Count different types of elements
    element_counts = {}
    for tag in soup.find_all():
        tag_name = tag.name
        element_counts[tag_name] = element_counts.get(tag_name, 0) + 1
    
    structure["element_counts"] = element_counts
    structure["total_elements"] = len(soup.find_all())
    
    # Find main structural elements
    structure["has_header"] = bool(soup.find(["header", "[role='banner']"]))
    structure["has_nav"] = bool(soup.find(["nav", "[role='navigation']"]))
    structure["has_main"] = bool(soup.find(["main", "[role='main']"]))
    structure["has_footer"] = bool(soup.find(["footer", "[role='contentinfo']"]))
    structure["has_aside"] = bool(soup.find(["aside", "[role='complementary']"]))
    
    # Count headings
    headings = {}
    for i in range(1, 7):
        headings[f"h{i}"] = len(soup.find_all(f"h{i}"))
    structure["headings"] = headings
    
    return structure

def extract_scripts(soup):
    """Extract all JavaScript code and script references"""
    scripts = []
    
    for script in soup.find_all("script"):
        script_info = {}
        
        # External script
        if script.get("src"):
            script_info["type"] = "external"
            script_info["src"] = script.get("src")
        else:
            # Inline script
            script_info["type"] = "inline"
            script_content = script.string or ""
            script_info["content"] = script_content
            script_info["content_length"] = len(script_content)
        
        # Script attributes
        script_info["attributes"] = dict(script.attrs) if script.attrs else {}
        
        scripts.append(script_info)
    
    return scripts

def extract_styles(soup):
    """Extract all CSS styles and stylesheet references"""
    styles = []
    
    # External stylesheets
    for link in soup.find_all("link", rel="stylesheet"):
        style_info = {
            "type": "external",
            "href": link.get("href", ""),
            "attributes": dict(link.attrs) if link.attrs else {}
        }
        styles.append(style_info)
    
    # Inline styles
    for style in soup.find_all("style"):
        style_content = style.string or ""
        style_info = {
            "type": "inline",
            "content": style_content,
            "content_length": len(style_content),
            "attributes": dict(style.attrs) if style.attrs else {}
        }
        styles.append(style_info)
    
    return styles

def extract_all_meta_tags(soup):
    """Extract all meta tags with their attributes"""
    meta_tags = []
    
    for meta in soup.find_all("meta"):
        meta_info = dict(meta.attrs) if meta.attrs else {}
        meta_tags.append(meta_info)
    
    return meta_tags

def extract_comments(html):
    """Extract HTML comments from the source"""
    comment_pattern = r'<!--(.*?)-->'
    comments = re.findall(comment_pattern, html, re.DOTALL)
    return [comment.strip() for comment in comments if comment.strip()]

def extract_doctype(html):
    """Extract DOCTYPE declaration"""
    doctype_pattern = r'<!DOCTYPE[^>]*>'
    doctype_match = re.search(doctype_pattern, html, re.IGNORECASE)
    return doctype_match.group(0) if doctype_match else ""

def extract_forms(soup):
    """Extract all form structures"""
    forms = []
    
    for form in soup.find_all("form"):
        form_info = {
            "attributes": dict(form.attrs) if form.attrs else {},
            "method": form.get("method", "GET").upper(),
            "action": form.get("action", ""),
            "inputs": []
        }
        
        # Extract form inputs
        for input_elem in form.find_all(["input", "select", "textarea", "button"]):
            input_info = {
                "tag": input_elem.name,
                "attributes": dict(input_elem.attrs) if input_elem.attrs else {}
            }
            form_info["inputs"].append(input_info)
        
        forms.append(form_info)
    
    return forms

def extract_data_attributes(soup):
    """Extract all data-* attributes from elements"""
    data_attrs = {}
    
    for element in soup.find_all():
        if element.attrs:
            for attr, value in element.attrs.items():
                if attr.startswith("data-"):
                    if attr not in data_attrs:
                        data_attrs[attr] = []
                    data_attrs[attr].append({
                        "value": value if isinstance(value, str) else " ".join(value),
                        "element": element.name,
                        "element_id": element.get("id", ""),
                        "element_class": element.get("class", [])
                    })
    
    return data_attrs

def extract_structured_data(soup, html, url):
    """Extract structured data like prices, ratings, travel info, etc."""
    structured = {}
    
    try:
        # Extract JSON-LD structured data
        json_scripts = soup.find_all("script", type="application/ld+json")
        if json_scripts:
            structured["json_ld"] = []
            for script in json_scripts:
                try:
                    json_data = json.loads(script.string)
                    structured["json_ld"].append(json_data)
                except:
                    pass

        # Extract prices (various formats)
        prices = extract_prices(soup)
        if prices:
            structured["prices"] = prices

        # Extract ratings and reviews
        ratings = extract_ratings(soup)
        if ratings:
            structured["ratings"] = ratings

        # Extract travel-specific data
        travel_data = extract_travel_data(soup)
        if travel_data:
            structured["travel"] = travel_data

        # Extract product/hotel information
        product_info = extract_product_info(soup)
        if product_info:
            structured["products"] = product_info

        # Extract contact information
        contact_info = extract_contact_info(soup)
        if contact_info:
            structured["contact"] = contact_info

        # Extract dates
        dates = extract_dates(soup)
        if dates:
            structured["dates"] = dates

    except Exception as e:
        structured["extraction_error"] = str(e)

    return structured

def extract_prices(soup):
    """Extract price information from various selectors"""
    prices = []
    
    # Common price selectors
    price_selectors = [
        '[data-price]', '[class*="price"]', '[id*="price"]',
        '.currency', '.amount', '.cost', '.rate', '.fare',
        '[aria-label*="price"]', '[aria-label*="cost"]',
        'span[class*="currency"]', 'div[class*="price"]'
    ]
    
    for selector in price_selectors:
        elements = soup.select(selector)
        for elem in elements:
            text = elem.get_text(strip=True)
            # Look for currency patterns
            price_match = re.search(r'[₹$€£¥₩]\s*[\d,]+(?:\.\d{2})?|\d+(?:,\d{3})*(?:\.\d{2})?\s*[₹$€£¥₩]', text)
            if price_match:
                prices.append({
                    "text": text,
                    "price": price_match.group(),
                    "element": elem.name,
                    "class": elem.get("class", [])
                })
    
    # Also check data attributes
    for elem in soup.find_all(attrs={"data-price": True}):
        prices.append({
            "data_price": elem.get("data-price"),
            "text": elem.get_text(strip=True),
            "element": elem.name
        })
    
    return prices[:10]  # Limit to 10 prices

def extract_ratings(soup):
    """Extract rating and review information"""
    ratings = []
    
    # Common rating selectors
    rating_selectors = [
        '[class*="rating"]', '[class*="star"]', '[class*="review"]',
        '[aria-label*="rating"]', '[aria-label*="star"]',
        '[data-rating]', '[data-stars]'
    ]
    
    for selector in rating_selectors:
        elements = soup.select(selector)
        for elem in elements:
            text = elem.get_text(strip=True)
            # Look for rating patterns (e.g., "4.5", "4/5", "4 stars")
            rating_match = re.search(r'(\d+(?:\.\d+)?)\s*(?:/\s*\d+|stars?|★)', text, re.IGNORECASE)
            if rating_match:
                ratings.append({
                    "text": text,
                    "rating": rating_match.group(1),
                    "element": elem.name,
                    "class": elem.get("class", [])
                })
    
    return ratings[:5]  # Limit to 5 ratings

def extract_travel_data(soup):
    """Extract travel-specific information"""
    travel = {}
    
    # Hotel/accommodation info
    hotel_selectors = [
        '[class*="hotel"]', '[class*="accommodation"]', '[class*="property"]',
        'h1', 'h2', 'h3'  # Often contain hotel names
    ]
    
    hotels = []
    for selector in hotel_selectors:
        elements = soup.select(selector)
        for elem in elements:
            text = elem.get_text(strip=True)
            if any(keyword in text.lower() for keyword in ['hotel', 'inn', 'resort', 'lodge', 'suite']):
                hotels.append({
                    "name": text,
                    "element": elem.name,
                    "class": elem.get("class", [])
                })
    
    if hotels:
        travel["hotels"] = hotels[:5]
    
    # Location information
    location_selectors = [
        '[class*="location"]', '[class*="address"]', '[class*="city"]',
        '[aria-label*="location"]', '[aria-label*="address"]'
    ]
    
    locations = []
    for selector in location_selectors:
        elements = soup.select(selector)
        for elem in elements:
            text = elem.get_text(strip=True)
            if text and len(text) > 5:  # Filter out empty or very short text
                locations.append({
                    "text": text,
                    "element": elem.name,
                    "class": elem.get("class", [])
                })
    
    if locations:
        travel["locations"] = locations[:5]
    
    return travel

def extract_product_info(soup):
    """Extract product/service information"""
    products = []
    
    # Look for product containers
    product_selectors = [
        '[class*="product"]', '[class*="item"]', '[class*="card"]',
        '[class*="listing"]', '[class*="result"]'
    ]
    
    for selector in product_selectors:
        elements = soup.select(selector)
        for elem in elements[:10]:  # Limit to 10 products
            product = {}
            
            # Extract name/title
            title_elem = elem.find(['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])
            if title_elem:
                product["name"] = title_elem.get_text(strip=True)
            
            # Extract description
            desc_elem = elem.find(['p', 'div'], class_=re.compile(r'desc|summary', re.I))
            if desc_elem:
                product["description"] = desc_elem.get_text(strip=True)[:200]  # Limit length
            
            # Extract image
            img_elem = elem.find('img')
            if img_elem and img_elem.get('src'):
                product["image"] = img_elem.get('src')
            
            if product:  # Only add if we found some data
                products.append(product)
    
    return products

def extract_contact_info(soup):
    """Extract contact information"""
    contact = {}
    
    # Phone numbers
    phone_pattern = r'(\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}'
    phone_matches = re.findall(phone_pattern, soup.get_text())
    if phone_matches:
        contact["phones"] = list(set(phone_matches[:3]))  # Limit to 3 unique phones
    
    # Email addresses
    email_pattern = r'\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b'
    email_matches = re.findall(email_pattern, soup.get_text())
    if email_matches:
        contact["emails"] = list(set(email_matches[:3]))  # Limit to 3 unique emails
    
    return contact

def extract_dates(soup):
    """Extract date information"""
    dates = []
    
    # Common date patterns
    date_patterns = [
        r'\b\d{1,2}[/-]\d{1,2}[/-]\d{2,4}\b',  # MM/DD/YYYY or DD/MM/YYYY
        r'\b\d{4}[/-]\d{1,2}[/-]\d{1,2}\b',    # YYYY/MM/DD
        r'\b(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+\d{1,2},?\s+\d{4}\b'  # Month DD, YYYY
    ]
    
    text = soup.get_text()
    for pattern in date_patterns:
        matches = re.findall(pattern, text, re.IGNORECASE)
        dates.extend(matches)
    
    return list(set(dates[:10]))  # Limit to 10 unique dates
    