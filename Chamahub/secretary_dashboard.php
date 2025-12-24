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
    <title>Secretary Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            color: #333;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background-color: #1e272e;
            color: #fff;
            padding: 20px;
            width: 260px;
            transition: all 0.3s ease;
        }
        .sidebar h2 {
            font-size: 22px;
            margin-bottom: 25px;
        }
        .sidebar a {
            display: block;
            color: #fff;
            padding: 10px;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 8px;
        }
        .sidebar a:hover {
            background-color: #485460;
        }

        .toggle-btn {
            display: none;
            background: #0fbcf9;
            color: #fff;
            text-align: center;
            padding: 10px;
            margin-bottom: 15px;
            cursor: pointer;
            border-radius: 5px;
        }

        /* Main content */
        .main {
            flex: 1;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .greeting {
            font-size: 26px;
            font-weight: bold;
        }

        .datetime {
            text-align: right;
        }

        #clock, #calendar {
            font-size: 16px;
            margin-top: 5px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .logout-btn {
            display: inline-block;
            margin-top: 25px;
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar {
                width: 100%;
            }
            .toggle-btn {
                display: block;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar" id="sidebar">
        <div class="toggle-btn" onclick="toggleSidebar()">☰ Menu</div>
        <h2>Secretary Panel</h2>
        <a href="#">Dashboard</a>
        <a href="#">Meeting Minutes</a>
        <a href="#">Upload Documents</a>
        <a href="#">Upcoming Meetings</a>
        <a href="#">Member Directory</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main">
        <div class="header">
            <div class="greeting">
                Hello, <?php echo htmlspecialchars($user['name']); ?> 👋
                <div style="font-size: 16px; margin-top: 5px;">
                    Role: <strong>Secretary</strong> of <strong><?php echo htmlspecialchars($user['chama_name']); ?></strong>
                </div>
            </div>
            <div class="datetime">
                <div id="clock"></div>
                <div id="calendar"></div>
            </div>
        </div>

        <div class="card">
            <h3>Secretary Tools</h3>
            <ul style="margin-top: 10px; line-height: 1.8;">
                <li>📋 Record and view meeting minutes</li>
                <li>📂 Upload shared chama documents</li>
                <li>📅 Manage and view meeting schedules</li>
                <li>🧑‍🤝‍🧑 Manage member communication</li>
            </ul>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("collapsed");
    }

    function updateClockAndCalendar() {
        const now = new Date();
        document.getElementById("clock").textContent = now.toLocaleTimeString();
        document.getElementById("calendar").textContent = now.toDateString();
    }

    setInterval(updateClockAndCalendar, 1000);
    updateClockAndCalendar();
</script>
</body>
</html>
