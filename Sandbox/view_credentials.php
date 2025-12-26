<?php
$host = "localhost";
$db   = "sandbox_testusers";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed");
}

// Fetch all credentials
$result = $conn->query("SELECT * FROM idmkyx_users ORDER BY create_at DESC");
$credentials = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $credentials[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>IDMerit Sandbox - View Credentials</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <style>
        :root {
            /* Light theme variables */
            --bg-primary: #f5f5f5;
            --bg-secondary: #ffffff;
            --text-primary: #333333;
            --text-secondary: #495057;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
            --border-light: #eee;
            --table-hover: #f8f9fa;
            --table-header: #f8f9fa;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            --input-bg: #ffffff;
            --input-border: #ddd;
            --hash-bg: #f8f9fa;
            --hash-border: #e9ecef;
        }

        [data-theme="dark"] {
            /* Dark theme variables */
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text-primary: #ffffff;
            --text-secondary: #e0e0e0;
            --text-muted: #b0b0b0;
            --border-color: #404040;
            --border-light: #404040;
            --table-hover: #3a3a3a;
            --table-header: #3a3a3a;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            --input-bg: #3a3a3a;
            --input-border: #555;
            --hash-bg: #3a3a3a;
            --hash-border: #555;
        }

        /* Force DataTables dark theme */
        [data-theme="dark"] .dataTables_wrapper,
        [data-theme="dark"] .dataTables_wrapper * {
            background: var(--bg-secondary) !important;
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .dataTables_scrollBody,
        [data-theme="dark"] .dataTables_scrollHead,
        [data-theme="dark"] .dataTables_scrollHeadInner table,
        [data-theme="dark"] .dataTables_scrollBody table,
        [data-theme="dark"] .dataTable {
            background: var(--bg-secondary) !important;
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .dataTable thead th {
            background: var(--table-header) !important;
            color: var(--text-primary) !important;
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .dataTable tbody td {
            background: var(--bg-secondary) !important;
            color: var(--text-primary) !important;
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .dataTable tbody tr {
            background: var(--bg-secondary) !important;
        }

        [data-theme="dark"] .dataTable tbody tr:hover,
        [data-theme="dark"] .dataTable tbody tr:hover td {
            background: var(--table-hover) !important;
        }

        [data-theme="dark"] .dt-buttons .dt-button {
            background: var(--table-header) !important;
            color: var(--text-primary) !important;
            border: 1px solid var(--border-color) !important;
        }

        [data-theme="dark"] .dt-buttons .dt-button:hover {
            background: var(--table-hover) !important;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .container {
            width: 100%;
            max-width: none;
            margin: 0;
            background: var(--bg-secondary);
            color: var(--text-primary);
            padding: 30px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            min-height: calc(100vh - 40px);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-light);
        }

        .page-title {
            margin: 0;
            color: var(--text-primary);
            font-size: 28px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .credentials-table {
            margin-top: 20px;
        }

        .details-column {
            max-width: none;
            word-wrap: break-word;
            white-space: pre-line;
        }

        .btn {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            background: #45a049;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: #2196F3;
            font-size: 16px;
            padding: 12px 24px;
        }

        .btn-primary:hover {
            background: #0b7dda;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .btn-danger {
            background: #f44336;
        }

        .btn-danger:hover {
            background: #da190b;
        }

        .theme-toggle {
            font-size: 14px;
            padding: 10px 16px;
        }

        .theme-icon {
            font-size: 16px;
        }

        .table-container {
            background: var(--bg-secondary);
            border-radius: 8px;
            box-shadow: none;
            overflow: hidden;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .dataTables_wrapper {
            padding: 0;
            width: 100%;
            color: var(--text-primary);
        }

        .dataTables_filter {
            margin-bottom: 20px;
        }

        .dataTables_filter input {
            padding: 8px 12px;
            border: 1px solid var(--input-border);
            border-radius: 4px;
            margin-left: 10px;
            background: var(--input-bg);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .dataTables_filter label {
            color: var(--text-primary);
        }

        .dataTables_length label {
            color: var(--text-primary);
        }

        .dataTables_length select {
            background: var(--input-bg);
            color: var(--text-primary);
            border: 1px solid var(--input-border);
            border-radius: 4px;
            padding: 4px 8px;
            margin: 0 5px;
        }

        .dataTables_info {
            color: var(--text-secondary);
        }

        .dataTables_paginate .paginate_button {
            color: var(--text-primary) !important;
            background: var(--bg-secondary) !important;
            border: 1px solid var(--border-color) !important;
        }

        .dataTables_paginate .paginate_button:hover {
            background: var(--table-hover) !important;
            border: 1px solid var(--border-color) !important;
        }

        .dataTables_paginate .paginate_button.current {
            background: #2196F3 !important;
            color: white !important;
            border: 1px solid #2196F3 !important;
        }

        #credentialsTable {
            width: 100% !important;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        #credentialsTable th {
            background: var(--table-header) !important;
            font-weight: 600;
            color: var(--text-primary) !important;
            border-bottom: 2px solid var(--border-color) !important;
            padding: 12px 8px;
        }

        #credentialsTable td {
            padding: 12px 8px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color) !important;
            color: var(--text-primary);
            background: var(--bg-secondary) !important;
        }

        #credentialsTable tbody tr:hover {
            background-color: var(--table-hover) !important;
        }

        #credentialsTable tbody tr:hover td {
            background-color: var(--table-hover) !important;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: none;
            overflow: hidden;
            width: 100%;
        }

        .dataTables_wrapper {
            padding: 0;
            width: 100%;
        }

        .dataTables_filter {
            margin-bottom: 20px;
        }

        .dataTables_filter input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-left: 10px;
        }

        #credentialsTable {
            width: 100% !important;
        }

        #credentialsTable th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding: 12px 8px;
        }

        #credentialsTable td {
            padding: 12px 8px;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }

        #credentialsTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .details-full {
            white-space: pre-line;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Saved Credentials</h1>
            <div class="header-actions">
                <button id="themeToggle" class="btn btn-secondary theme-toggle">
                    <span class="theme-icon">🌙</span> Dark Mode
                </button>
                <a href="password_hash.php" class="btn btn-primary">
                    <span>+</span> Generate Password
                </a>
            </div>
        </div>

        <div class="table-container">
            <table id="credentialsTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Hash Password</th>
                        <th>Service Details</th>
                        <th>Expiring At</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($credentials as $cred): ?>
                        <tr>
                            <td><?= htmlspecialchars($cred['username']) ?></td>
                            <td><?= htmlspecialchars($cred['password']) ?></td>
                            <td>
                                <div class="hash-preview" title="<?= htmlspecialchars($cred['hash_password']) ?>">
                                    <?= htmlspecialchars($cred['hash_password']) ?>
                                </div>
                            </td>
                            <td class="details-column">
                                <div class="details-preview"><?= htmlspecialchars($cred['details']) ?></div>
                            </td>
                            <td><?= htmlspecialchars(date('Y-m-d', strtotime($cred['expire_at']))) ?></td>
                            <td><?= htmlspecialchars($cred['create_at']) ?></td>
                            <td>
                                <button onclick="deleteRecord(<?= $cred['id'] ?? 0 ?>)" class="btn btn-danger btn-small">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#credentialsTable').DataTable({
                "pageLength": 5,
                "order": [
                    [4, "desc"]
                ],
                "columnDefs": [{
                        "orderable": false,
                        "targets": [5]
                    },
                    {
                        "width": "15%",
                        "targets": [0]
                    },
                    {
                        "width": "12%",
                        "targets": [1]
                    },
                    {
                        "width": "20%",
                        "targets": [2]
                    },
                    {
                        "width": "35%",
                        "targets": [3]
                    },
                    {
                        "width": "12%",
                        "targets": [4]
                    },
                    {
                        "width": "6%",
                        "targets": [5]
                    }
                ],
                "dom": 'Bfrtip',

                "responsive": true,
                "scrollX": true,
                "autoWidth": false,
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ credentials",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });
        });

        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('.theme-icon');
        const body = document.body;

        // Check for saved theme preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            body.setAttribute('data-theme', 'dark');
            themeIcon.textContent = '☀️';
            themeToggle.innerHTML = '<span class="theme-icon">☀️</span> Light Mode';
        }

        themeToggle.addEventListener('click', function() {
            const currentTheme = body.getAttribute('data-theme');

            if (currentTheme === 'dark') {
                body.removeAttribute('data-theme');
                themeIcon.textContent = '🌙';
                themeToggle.innerHTML = '<span class="theme-icon">🌙</span> Dark Mode';
                localStorage.setItem('theme', 'light');
            } else {
                body.setAttribute('data-theme', 'dark');
                themeIcon.textContent = '☀️';
                themeToggle.innerHTML = '<span class="theme-icon">☀️</span> Light Mode';
                localStorage.setItem('theme', 'dark');
            }

            // Force DataTables to redraw with new theme
            setTimeout(() => {
                $('#credentialsTable').DataTable().draw();
            }, 100);
        });

        function deleteRecord(id) {
            if (confirm('Are you sure you want to delete this record?')) {
                fetch('delete_credential.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=' + id
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error deleting record: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting record');
                    });
            }
        }

        // Add comprehensive DataTables theme override
        const dataTablesStyle = document.createElement('style');
        dataTablesStyle.textContent = `
            /* Comprehensive DataTables theming */
            .dataTables_wrapper {
                background: var(--bg-secondary) !important;
                color: var(--text-primary) !important;
            }
            
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_processing,
            .dataTables_wrapper .dataTables_paginate {
                color: var(--text-primary) !important;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                background: var(--bg-secondary) !important;
                color: var(--text-primary) !important;
                border: 1px solid var(--border-color) !important;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: var(--table-hover) !important;
                color: var(--text-primary) !important;
                border: 1px solid var(--border-color) !important;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background: #2196F3 !important;
                color: white !important;
                border: 1px solid #2196F3 !important;
            }
            
            table.dataTable {
                background: var(--bg-secondary) !important;
                color: var(--text-primary) !important;
            }
            
            table.dataTable thead th,
            table.dataTable thead td {
                background: var(--table-header) !important;
                color: var(--text-primary) !important;
                border-bottom: 1px solid var(--border-color) !important;
            }
            
            table.dataTable tbody th,
            table.dataTable tbody td {
                background: var(--bg-secondary) !important;
                color: var(--text-primary) !important;
                border-top: 1px solid var(--border-color) !important;
            }
            
            table.dataTable.stripe tbody tr.odd,
            table.dataTable.display tbody tr.odd {
                background: var(--bg-secondary) !important;
            }
            
            table.dataTable.stripe tbody tr.even,
            table.dataTable.display tbody tr.even {
                background: var(--bg-secondary) !important;
            }
            
            table.dataTable.hover tbody tr:hover,
            table.dataTable.display tbody tr:hover {
                background: var(--table-hover) !important;
            }
            
            table.dataTable.hover tbody tr:hover > .sorting_1,
            table.dataTable.display tbody tr:hover > .sorting_1 {
                background: var(--table-hover) !important;
            }
        `;
        document.head.appendChild(dataTablesStyle);

        // Add some custom styles for small buttons
        const style = document.createElement('style');
        style.textContent = `
            .btn-small {
                padding: 6px 12px;
                font-size: 12px;
                border: none;
                background: #f44336;
                color: white;
                border-radius: 4px;
                cursor: pointer;
                margin: 2px;
                transition: all 0.2s ease;
            }
            .btn-small:hover {
                background: #da190b;
                transform: translateY(-1px);
            }
            .btn-sm {
                padding: 6px 12px !important;
                font-size: 12px !important;
                margin: 2px !important;
                background: var(--bg-secondary) !important;
                color: var(--text-primary) !important;
                border: 1px solid var(--border-color) !important;
            }
            .btn-sm:hover {
                background: var(--table-hover) !important;
            }
            .hash-preview {
                font-family: 'Courier New', monospace;
                font-size: 11px;
                background: var(--hash-bg);
                color: var(--text-primary);
                padding: 4px 6px;
                border-radius: 3px;
                border: 1px solid var(--hash-border);
                display: inline-block;
                max-width: 200px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            
            /* Light theme hash preview */
            .hash-preview {
                background: #f8f9fa !important;
                color: #495057 !important;
                border: 1px solid #e9ecef !important;
            }
            
            /* Dark theme hash preview */
            [data-theme="dark"] .hash-preview {
                background: #3a3a3a !important;
                color: #ffffff !important;
                border: 1px solid #555 !important;
            }
            .details-preview {
                font-size: 12px;
                line-height: 1.4;
                color: var(--text-secondary);
                white-space: pre-line;
            }
            .dt-buttons {
                margin-bottom: 15px;
            }
            .dt-button {
                margin-right: 5px !important;
                background: var(--bg-secondary) !important;
                color: var(--text-primary) !important;
                border: 1px solid var(--border-color) !important;
                padding: 6px 12px !important;
                border-radius: 4px !important;
                font-size: 12px !important;
            }
            .dt-button:hover {
                background: var(--table-hover) !important;
                color: var(--text-primary) !important;
            }
            
            /* Light theme button fixes */
            .dt-buttons .dt-button {
                background: #f8f9fa !important;
                color: #495057 !important;
                border: 1px solid #dee2e6 !important;
            }
            
            .dt-buttons .dt-button:hover {
                background: #e9ecef !important;
                color: #495057 !important;
            }
            
            /* Dark theme button overrides */
            [data-theme="dark"] .dt-buttons .dt-button {
                background: #3a3a3a !important;
                color: #ffffff !important;
                border: 1px solid #555 !important;
            }
            
            [data-theme="dark"] .dt-buttons .dt-button:hover {
                background: #4a4a4a !important;
                color: #ffffff !important;
            }
            
            @media (max-width: 768px) {
                body {
                    padding: 10px;
                }
                
                .container {
                    padding: 15px;
                    min-height: calc(100vh - 20px);
                }
                
                .page-header {
                    flex-direction: column;
                    gap: 15px;
                    text-align: center;
                }
                
                .btn-primary {
                    width: 100%;
                    justify-content: center;
                }
                
                .theme-toggle {
                    width: 100%;
                    justify-content: center;
                }
                
                #credentialsTable {
                    font-size: 12px;
                }
                
                .btn-small {
                    padding: 4px 8px !important;
                    font-size: 10px !important;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>