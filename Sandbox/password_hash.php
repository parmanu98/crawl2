<?php
$host = "localhost";
$db   = "sandbox_testusers";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed");
}
$expiryDate = date("Y-m-d", strtotime("+45 days"));

$username = $_POST['username'] ?? $_GET['username'] ?? '';
$password = $_POST['password'] ?? $_GET['password'] ?? '';
$service  = $_POST['service'] ?? $_GET['service'] ?? '';
$hash     = '';
$saved    = false;

$generatedHash = '';
if ($password) {
    $generatedHash = password_hash($password, PASSWORD_DEFAULT);
}

if (isset($_POST['save'])) {
    
    $expiryDate = date("Y-m-d", strtotime("+45 days"));
    
    $stmt = $conn->prepare(
        "INSERT INTO idmkyx_users
        (username, password, hash_password, details, create_at, expire_at)
        VALUES (?, ?, ?, ?, NOW(6), ?)"
    );

    // Get service details based on selected service
    $serviceDetails = '';
    $service = $_POST['service'];

    switch($service) {
        case 'IDMaml':
            $serviceDetails = "Service: IDMaml\n";
            $serviceDetails .= "Base URL: https://amldev2.idmerit.com/\n";
            $serviceDetails .= "Ping Test: https://amldev2.idmerit.com/v1.4/ping\n";
            $serviceDetails .= "API Documentation: https://idmaml-doc.idmerit.com/\n";
            $serviceDetails .= "Web Interface: https://amldev2.idmerit.com/AmlWeb\n";
            $serviceDetails .= "Request Hits: 10\n";
            $serviceDetails .= "Username: " . $_POST['username'] . "\n";
            $serviceDetails .= "Expires: " . $expiryDate . "\n";
            $serviceDetails .= "Note: Password will be sent in the comment";
            break;
            
        case 'IDMkyb':
            $serviceDetails = "Service: IDMkyb\n";
            $serviceDetails .= "Base URL: https://kybdev.idmerit.com/\n";
            $serviceDetails .= "Ping Test: https://kybdev.idmerit.com/v1.4/ping\n";
            $serviceDetails .= "API Documentation: https://idmkyb-doc.idmerit.com\n";
            $serviceDetails .= "Web Interface: https://kybdev.idmerit.com/demo/login\n";
            $serviceDetails .= "Request Hits: 20\n";
            $serviceDetails .= "Username: " . $_POST['username'] . "\n";
            $serviceDetails .= "Expires: " . $expiryDate . "\n";
            $serviceDetails .= "Note: Password will be sent in the comment";
            break;
            
        case 'IDMtrust':
            $serviceDetails = "Service: IDMtrust\n";
            $serviceDetails .= "Base URL: https://itrust-dev.idmerit.com\n";
            $serviceDetails .= "Ping Test: https://itrust-dev.idmerit.com/v2.1/ping\n";
            $serviceDetails .= "API Documentation: https://idmtrust-doc.idmerit.com/\n";
            $serviceDetails .= "Web Interface: https://itrust-dev.idmerit.com/trustWeb\n";
            $serviceDetails .= "Request Hits: 20\n";
            $serviceDetails .= "Username: " . $_POST['username'] . "\n";
            $serviceDetails .= "Expires: " . $expiryDate . "\n";
            $serviceDetails .= "Note: Password will be sent in the comment";
            break;
            
        case 'IDMsocial':
            $serviceDetails = "Service: IDMsocial\n";
            $serviceDetails .= "Base URL: https://idsocial-dev.idmerit.com\n";
            $serviceDetails .= "Ping Test: https://idsocial-dev.idmerit.com/v2.1/ping\n";
            $serviceDetails .= "API Documentation: https://idmsocial-doc.idmerit.com\n";
            $serviceDetails .= "Web Interface: https://idsocial-dev.idmerit.com/socialWeb\n";
            $serviceDetails .= "Request Hits: 20\n";
            $serviceDetails .= "Username: " . $_POST['username'] . "\n";
            $serviceDetails .= "Expires: " . $expiryDate . "\n";
            $serviceDetails .= "Note: Password will be sent in the comment";
            break;
            
        default:
            $serviceDetails = "Service: " . $service;
    }

    $stmt->bind_param(
        "sssss",
        $_POST['username'],
        $_POST['password'],
        $_POST['hash_password'],
        $serviceDetails,
        $expiryDate
    );

    $stmt->execute();
    $saved = true;
}


?>

<html>

<head>
    <title>IDMerit Sandbox User Portal</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h1>IDMerit Sandbox Portal</h1>

        <div class="action-buttons" style="margin-bottom: 20px;">
            <a href="view_credentials.php" class="copy-btn" style="text-decoration: none; display: inline-block;">View Saved Credentials</a>
        </div>

        <form method="get" action="password_hash.php">
            <label>Select Service</label>
            <select name="service" id="serviceSelect" onchange="showServiceInfo(this.value)">
                <option value="">Choose a service...</option>
                <option value="IDMaml">IDMaml</option>
                <option value="IDMkyb">IDMkyb</option>
                <option value="IDMtrust">IDMtrust</option>
                <option value="IDMsocial">IDMsocial</option>
            </select>

            <label>Password</label>
            <input type="text" name="password" id="password" placeholder="Enter password" value="<?= htmlspecialchars($password) ?>" required>
            <label>Username</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($username) ?>" required>
            <button type="submit">Generate Hash</button>
        </form>

        <?php if ($generatedHash): ?>
            <div class="result-box">
                <label>Generated Hash</label>
                <div class="copy-container">
                    <input type="text" id="hashOutput" value="<?php echo htmlspecialchars($generatedHash); ?>" readonly>
                    <button type="button" onclick="copyHash()" class="copy-btn">Copy</button>
                </div>
            </div>

            <form method="post" action="password_hash.php">
                <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
                <input type="hidden" name="password" value="<?= htmlspecialchars($password) ?>">
                <input type="hidden" name="service" value="<?= htmlspecialchars($service) ?>">
                <input type="hidden" name="hash_password" value="<?= htmlspecialchars($generatedHash) ?>">
                <button type="submit" name="save" class="copy-btn">Save</button>
            </form>
            <button type="button" onclick="resetForm()" class="copy-btn reset-btn">Reset All</button>
        <?php endif; ?>
        <?php if ($saved): ?>
            <p class="success-msg">✔ Saved successfully</p>
        <?php endif; ?>
        <div id="serviceInfo" class="service-info"></div>
    </div>

    <script>
        const serviceData = {
            'IDMaml': {
                title: 'IDMaml',
                baseUrl: 'https://amldev2.idmerit.com/',
                pingTest: 'https://amldev2.idmerit.com/v1.4/ping',
                apiDoc: 'https://idmaml-doc.idmerit.com/',
                webPage: 'https://amldev2.idmerit.com/AmlWeb',
                hits: '10'
            },
            'IDMkyb': {
                title: 'IDMkyb',
                baseUrl: 'https://kybdev.idmerit.com/',
                pingTest: 'https://kybdev.idmerit.com/v1.4/ping',
                apiDoc: 'https://idmkyb-doc.idmerit.com',
                webPage: 'https://kybdev.idmerit.com/demo/login',
                hits: '20'
            },
            'IDMtrust': {
                title: 'IDMtrust',
                baseUrl: 'https://itrust-dev.idmerit.com',
                pingTest: 'https://itrust-dev.idmerit.com/v2.1/ping',
                apiDoc: 'https://idmtrust-doc.idmerit.com/',
                webPage: 'https://itrust-dev.idmerit.com/trustWeb',
                hits: '20'
            },
            'IDMsocial': {
                title: 'IDMsocial',
                baseUrl: 'https://idsocial-dev.idmerit.com',
                pingTest: 'https://idsocial-dev.idmerit.com/v2.1/ping',
                apiDoc: 'https://idmsocial-doc.idmerit.com',
                webPage: 'https://idsocial-dev.idmerit.com/socialWeb',
                hits: '20'
            }
        };

        function showServiceInfo(service) {
            const infoDiv = document.getElementById('serviceInfo');

            if (!service || !serviceData[service]) {
                infoDiv.classList.remove('active');
                return;
            }

            const data = serviceData[service];
            const expiryDate = '<?php echo date("Y-m-d", strtotime("+45 days")); ?>';
            const username = document.getElementById('username').value || '<?= htmlspecialchars($username) ?>';

            const serviceText = `Service: ${data.title}
                                Base URL: ${data.baseUrl}
                                Ping Test: ${data.pingTest}
                                API Documentation: ${data.apiDoc}
                                Web Interface: ${data.webPage}
                                Request Hits: ${data.hits}
                                Username: ${username}
                                Expires: ${expiryDate}
                                Note: Password will be sent in the comment`;

            infoDiv.innerHTML = `
                <div class="info-header">
                    <h3>${data.title} Details</h3>
                    <button type="button" onclick="copyServiceDetails()" class="copy-btn">Copy All</button>
                </div>
                
                <div class="info-content">
                    <p><strong>Base URL:</strong><br><a href="${data.baseUrl}" target="_blank">${data.baseUrl}</a></p>
                    <p><strong>Ping Test:</strong><br><a href="${data.pingTest}" target="_blank">${data.pingTest}</a></p>
                    <p><strong>API Documentation:</strong><br><a href="${data.apiDoc}" target="_blank">${data.apiDoc}</a></p>
                    <p><strong>Web Interface:</strong><br><a href="${data.webPage}" target="_blank">${data.webPage}</a></p>
                    <p><strong>Username:</strong> ${username}</p>
                    <p><strong>Request Hits:</strong> ${data.hits}</p>
                    <p><strong>Expires:</strong> ${expiryDate}</p>
                    <p><em>Password will be sent in the comment</em></p>
                </div>
                <textarea id="serviceDetailsText" style="position:absolute;left:-9999px;">${serviceText}</textarea>
            `;

            infoDiv.classList.add('active');
        }

        function copyHash() {
            const hashInput = document.getElementById('hashOutput');
            hashInput.select();
            document.execCommand('copy');

            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = 'Copied!';
            btn.style.background = '#10b981';

            setTimeout(() => {
                btn.textContent = originalText;
                btn.style.background = '';
            }, 2000);
        }

        function copyServiceDetails() {
            const textarea = document.getElementById('serviceDetailsText');
            textarea.select();
            document.execCommand('copy');

            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = 'Copied!';
            btn.style.background = '#10b981';

            setTimeout(() => {
                btn.textContent = originalText;
                btn.style.background = '';
            }, 2000);
        }

        function resetForm() {
            window.location.href = 'password_hash.php';
        }

        <?php if ($service): ?>
            showServiceInfo("<?= $service ?>");
        <?php endif; ?>
    </script>
</body>

</html>