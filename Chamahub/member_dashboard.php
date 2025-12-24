<?php
session_start();

// Validate session
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user']; // must contain id, role, name, chama_id, chama_name
$allowedRoles = ['member'];

if (!in_array($user['role'], $allowedRoles)) {
    header("Location: dashboard.php");
    exit();
}

require_once __DIR__ . '/includes/db_chamahub.php';

$member_id = $user['id'];
$chama_id = $user['chama_id'];
$total_contributed = 0;

// Fetch contribution total for this member in this chama
$stmt = $conn->prepare("SELECT SUM(amount) AS total FROM contributions WHERE user_id = ? AND chama_id = ?");
$stmt->bind_param("ii", $member_id, $chama_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $total_contributed = $row['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            color: #2c3e50;
            transition: background-color 0.3s, color 0.3s;
        }
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            height: 100%;
            width: 220px;
            background-color: #2c3e50;
            color: #ecf0f1;
            padding-top: 60px;
            transition: all 0.3s ease-in-out;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: #ecf0f1;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .toggle-btn, .darkmode-btn {
            position: absolute;
            top: 15px;
            background-color: #2c3e50;
            color: white;
            border: none;
            font-size: 20px;
            padding: 5px 10px;
            cursor: pointer;
            z-index: 999;
        }
        .toggle-btn { left: 15px; }
        .darkmode-btn { left: 60px; }

        .main {
            margin-left: 220px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        .collapsed .sidebar { width: 60px; }
        .collapsed .sidebar a .label { display: none; }
        .collapsed .main { margin-left: 60px; }

        .dashboard-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 700px;
            margin: auto;
        }

        .dashboard-card h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .info, .greeting, .clock {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .calendar {
            margin-top: 10px;
            font-size: 16px;
        }

        .logout {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .main { margin-left: 0; }
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
        }

        .dark-mode {
            background-color: #1e272e;
            color: #ecf0f1;
        }
        .dark-mode .dashboard-card {
            background-color: #2c3e50;
            color: #ecf0f1;
        }
        #burger {
            display: none;
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            z-index: 1000;
        }

        @media (max-width: 768px) {
            #burger {
                display: block;
            }
        }
    </style>
</head>
<body>
    <button class="toggle-btn" onclick="toggleMenu()">☰</button>
    <button class="darkmode-btn" onclick="toggleDarkMode()">🌓</button>
    <div id="burger" onclick="toggleMenu()">☰</div>

    <div class="sidebar" id="sidebar">
        <a href="#"><span>🏠</span> <span class="label">Dashboard</span></a>
        <a href="#"><span>💼</span> <span class="label">My Contributions</span></a>
        <a href="#"><span>📅</span> <span class="label">Events</span></a>
        <a href="#"><span>📊</span> <span class="label">Reports</span></a>
        <a href="logout.php"><span>🚪</span> <span class="label">Logout</span></a>
    </div>

    <div class="main" id="main">
        <div class="dashboard-card">
            <div class="greeting" id="greeting"></div>
            <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?> 👋</h1>
            <p class="info">You are a member of: <strong><?php echo htmlspecialchars($user['chama_name']); ?></strong></p>
            <div class="clock" id="clock"></div>
            <div class="calendar" id="calendar"></div>

            <hr>

            <h2>🪙 Contributions Summary</h2>
            <p>Total Contributed: <strong>KES <?php echo number_format($total_contributed, 2); ?></strong></p>

            <h2>📅 Upcoming Events</h2>
            <ul>
                <li>AGM - 5th Aug</li>
                <li>Monthly Meeting - 12th Aug</li>
            </ul>

            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>

    <script>
        function toggleMenu() {
            document.body.classList.toggle('collapsed');
        }
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }
        function updateClock() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeStr = `${hours}:${minutes}:${seconds}`;
            document.getElementById('clock').innerText = "⏰ Current Time: " + timeStr;

            const greeting = document.getElementById('greeting');
            if (hours < 12) greeting.innerText = "🌅 Good Morning!";
            else if (hours < 18) greeting.innerText = "🌞 Good Afternoon!";
            else greeting.innerText = "🌙 Good Evening!";
        }
        function showCalendar() {
            const date = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = date.toLocaleDateString(undefined, options);
            document.getElementById('calendar').innerText = "📅 Today: " + formattedDate;
        }

        setInterval(updateClock, 1000);
        updateClock();
        showCalendar();
    </script>
</body>
</html>
