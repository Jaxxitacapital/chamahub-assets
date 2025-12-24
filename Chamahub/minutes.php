<?php
session_start();
if (!isset($_SESSION['user_data'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user_data'];
if ($user['role'] !== 'secretary') {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meeting Minutes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f9fc;
            padding: 30px;
            color: #333;
        }

        h2 {
            font-size: 26px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #1e272e;
            color: white;
        }

        .add-btn {
            background: #0fbcf9;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 10px;
            border-radius: 6px;
        }

        .back-btn {
            margin-top: 20px;
            display: inline-block;
            background: #485460;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<h2>📋 Meeting Minutes</h2>
<a href="#" class="add-btn">+ Add New Minute</a>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Title</th>
            <th>Summary</th>
            <th>Recorded By</th>
        </tr>
    </thead>
    <tbody>
        <!-- Example static rows -->
        <tr>
            <td>2025-07-25</td>
            <td>Monthly Planning</td>
            <td>Discussed savings, new members, and upcoming trip.</td>
            <td>Secretary - <?php echo htmlspecialchars($user['name']); ?></td>
        </tr>
    </tbody>
</table>

<a href="secretary_dashboard.php" class="back-btn">← Back to Dashboard</a>

</body>
</html>
