let currentResults = null;
let currentHtmlSource = null;

async function startCrawl() {
  const url = document.getElementById("url").value;
  const depth = Number(document.getElementById("depth").value);
  const sameDomain = document.getElementById("same_domain").checked;

  const fields = Array.from(
    document.querySelectorAll("input[type=checkbox]:checked")
  ).map(cb => cb.value)
    .filter(value => value !== "on"); // Filter out the same_domain checkbox value

  // Validation
  if (!url) {
    showError("Please enter a valid URL");
    return;
  }

  const payload = {
    seed_url: url,
    depth: depth,
    same_domain: sameDomain,
    fields: fields
  };

  console.log("Sending:", payload);

  // Disable button and show loading state
  const button = document.querySelector('.crawl-button');
  const originalText = button.innerHTML;
  button.disabled = true;
  button.innerHTML = '<div class="loading-spinner"></div> Crawling...';

  // Show initial status
  showStatus('running', 'Starting crawl...');
  clearResults();

  try {
    const res = await fetch("http://localhost:8000/crawl", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(payload)
    });

    const data = await res.json();

    if (data.job_id) {
      showStatus('running', `Job started with ID: ${data.job_id}`);

      // Start polling for status
      pollJobStatus(data.job_id);
    } else {
      showError("Failed to start crawl job");
      resetButton(button, originalText);
    }
  } catch (error) {
    showError(`Network error: ${error.message}`);
    resetButton(button, originalText);
  }
}

async function pollJobStatus(jobId) {
  try {
    const res = await fetch(`http://localhost:8000/status/${jobId}`);
    const status = await res.json();

    console.log("Status:", status);

    if (status.status === "completed") {
      showStatus('completed', 'Crawl completed successfully!');
      displayResults(status.result);
      resetButton();
    } else if (status.status === "failed") {
      showStatus('failed', `Crawl failed: ${status.error || "Unknown error"}`);
      resetButton();
    } else {
      // Still running, update status and poll again
      showStatus('running', `Status: ${status.status} - Checking again in 2 seconds...`);
      setTimeout(() => pollJobStatus(jobId), 2000);
    }
  } catch (error) {
    showError(`Error checking status: ${error.message}`);
    resetButton();
  }
}

function showStatus(type, message) {
  const statusContainer = document.getElementById('statusContainer');
  const icon = type === 'running' ? 'fas fa-spinner fa-spin' :
    type === 'completed' ? 'fas fa-check-circle' :
      'fas fa-exclamation-triangle';

  statusContainer.innerHTML = `
    <div class="status-indicator status-${type}">
      <i class="${icon}"></i>
      ${message}
    </div>
  `;
}

function showError(message) {
  showStatus('failed', message);
}

function displayResults(results) {
  currentResults = results;
  const outputContainer = document.getElementById('outputContainer');
  const copyButton = document.getElementById('copyButton');
  const downloadButton = document.getElementById('downloadButton');

  if (!results || results.length === 0) {
    outputContainer.innerHTML = `
      <div class="empty-state">
        <i class="fas fa-exclamation-circle"></i>
        <h3>No Data Found</h3>
        <p>The crawl completed but no data was extracted from the target website</p>
      </div>
    `;
    copyButton.disabled = true;
    downloadButton.style.display = 'none';
    return;
  }

  // Check if HTML source is included
  let hasHtmlSource = false;
  currentHtmlSource = null;

  if (results.length > 0 && results[0].html_source) {
    hasHtmlSource = true;
    currentHtmlSource = results[0].html_source.raw_html;
    downloadButton.style.display = 'flex';
    downloadButton.disabled = false;
  } else {
    downloadButton.style.display = 'none';
  }

  // Create a copy of results without the raw HTML for display (to avoid UI lag)
  const displayResults = results.map(item => {
    const copy = { ...item };
    if (copy.html_source && copy.html_source.raw_html) {
      copy.html_source = {
        ...copy.html_source,
        raw_html: `[HTML Source - ${copy.html_source.html_length} characters] (Use Download HTML button to get full source)`
      };
    }
    return copy;
  });

  // Format JSON with syntax highlighting
  const formattedJson = JSON.stringify(displayResults, null, 2);

  outputContainer.innerHTML = `
    <div class="json-output">${syntaxHighlight(formattedJson)}</div>
  `;

  copyButton.disabled = false;
}

function syntaxHighlight(json) {
  json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
    var cls = 'number';
    if (/^"/.test(match)) {
      if (/:$/.test(match)) {
        cls = 'key';
      } else {
        cls = 'string';
      }
    } else if (/true|false/.test(match)) {
      cls = 'boolean';
    } else if (/null/.test(match)) {
      cls = 'null';
    }
    return '<span class="' + cls + '">' + match + '</span>';
  });
}

function clearResults() {
  const outputContainer = document.getElementById('outputContainer');
  outputContainer.innerHTML = `
    <div class="empty-state">
      <i class="fas fa-hourglass-half"></i>
      <h3>Crawling in Progress</h3>
      <p>Please wait while we extract data from the website...</p>
    </div>
  `;

  document.getElementById('copyButton').disabled = true;
  document.getElementById('downloadButton').style.display = 'none';
  currentResults = null;
  currentHtmlSource = null;
}

function resetButton(button = null, originalText = null) {
  const btn = button || document.querySelector('.crawl-button');
  const text = originalText || '<i class="fas fa-play"></i> Start Crawling';

  btn.disabled = false;
  btn.innerHTML = text;
}

async function copyResults() {
  if (!currentResults) {
    showError("No results to copy");
    return;
  }

  try {
    const jsonString = JSON.stringify(currentResults, null, 2);
    await navigator.clipboard.writeText(jsonString);

    // Show success feedback
    const copyButton = document.getElementById('copyButton');
    const originalText = copyButton.innerHTML;
    copyButton.innerHTML = '<i class="fas fa-check"></i> Copied!';
    copyButton.style.background = '#28a745';

    setTimeout(() => {
      copyButton.innerHTML = originalText;
      copyButton.style.background = '#28a745';
    }, 2000);

  } catch (error) {
    showError("Failed to copy to clipboard");
  }
}

function downloadHtmlSource() {
  if (!currentHtmlSource) {
    showError("No HTML source available to download");
    return;
  }

  try {
    // Create a blob with the HTML content
    const blob = new Blob([currentHtmlSource], { type: 'text/html' });

    // Create a download link
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;

    // Generate filename from URL or use default
    const urlObj = new URL(currentResults[0].url);
    const filename = `${urlObj.hostname.replace(/\./g, '_')}_source.html`;
    a.download = filename;

    // Trigger download
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    // Show success feedback
    const downloadButton = document.getElementById('downloadButton');
    const originalText = downloadButton.innerHTML;
    downloadButton.innerHTML = '<i class="fas fa-check"></i> Downloaded!';

    setTimeout(() => {
      downloadButton.innerHTML = originalText;
    }, 2000);

  } catch (error) {
    showError("Failed to download HTML source");
  }
}

// Add CSS for syntax highlighting
const style = document.createElement('style');
style.textContent = `
  .json-output .string { color: #ce9178; }
  .json-output .number { color: #b5cea8; }
  .json-output .boolean { color: #569cd6; }
  .json-output .null { color: #569cd6; }
  .json-output .key { color: #9cdcfe; }
`;
document.head.appendChild(style);