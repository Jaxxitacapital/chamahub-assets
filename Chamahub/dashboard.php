<?php
session_start();
require_once(__DIR__ . '/includes/auth.php');
require_once(__DIR__ . '/includes/db_chamahub.php');

$user = $_SESSION['user_data'] ?? null;
if (!$user || strtolower($user['role']) !== 'chairman') {
    header("Location: index.php");
    exit();
}

$chairman_id = $user['id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = strtolower(trim($_POST['role']));
    $allowed_roles = ['member', 'admin', 'chairman'];

    if (!in_array($role, $allowed_roles)) {
        $role = 'member';
    }

    if ($password !== $confirm_password) {
        $message = "❌ Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "⚠️ Email already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, chairman_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssssi", $name, $email, $hashed_password, $role, $chairman_id);
            if ($stmt->execute()) {
                $message = "✅ Member added successfully as $role!";
            } else {
                $message = "❌ Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT id, name, email, role, created_at FROM users WHERE chairman_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $chairman_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Chairman Dashboard | ChamaHub</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script> tailwind.config = { darkMode: 'class' };</script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen font-sans transition-colors">

<!-- Dark Mode Toggle -->
<button id="darkToggle" class="fixed top-5 right-5 p-3 rounded-full bg-indigo-600 text-white shadow-lg hover:bg-indigo-700 z-50" title="Toggle Dark Mode">🌙</button>

<div class="flex min-h-screen">
  <!-- Sidebar -->
  <aside id="sidebar" class="bg-white dark:bg-gray-800 w-64 p-8 shadow-lg flex flex-col fixed md:relative md:h-auto h-full z-40">
    <div class="flex justify-between mb-10">
      <h1 class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">ChamaHub</h1>
      <button id="sidebarToggle" class="md:hidden p-2 text-indigo-600 dark:text-indigo-400">✕</button>
    </div>
    <nav class="flex flex-col space-y-6 text-lg font-semibold text-gray-700 dark:text-gray-300">
      <a href="#" class="flex items-center gap-2 hover:text-indigo-600 dark:hover:text-indigo-400">🏠 Dashboard</a>
      <a href="#" class="flex items-center gap-2 hover:text-indigo-600 dark:hover:text-indigo-400">👥 Manage Members</a>
      <a href="logout.php" class="flex items-center gap-2 text-red-600 dark:text-red-400">🚪 Logout</a>
    </nav>
    <div class="mt-auto pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
      <div id="clock" class="font-semibold text-indigo-600"></div>
      <div id="calendar" class="text-indigo-500"></div>
    </div>
  </aside>

  <!-- Main -->
  <main class="flex-1 ml-0 md:ml-64 p-8 max-w-7xl mx-auto w-full transition-all duration-400">
    <h1 class="text-4xl font-extrabold mb-4">Welcome, <?= htmlspecialchars($user['name']) ?> 👋</h1>

    <?php if ($message): ?>
      <div class="mb-6 p-4 bg-indigo-600 text-white rounded shadow"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add Member Form -->
    <section class="mb-12 bg-white dark:bg-gray-800 rounded-lg shadow p-8 max-w-md">
      <h2 class="text-2xl font-bold mb-6">Add New Member</h2>
      <form method="POST" class="space-y-6">
        <input type="text" name="name" placeholder="Full Name" required class="w-full p-3 rounded border bg-gray-50 dark:bg-gray-700" />
        <input type="email" name="email" placeholder="Email" required class="w-full p-3 rounded border bg-gray-50 dark:bg-gray-700" />
        <input type="password" name="password" placeholder="Password" required class="w-full p-3 rounded border bg-gray-50 dark:bg-gray-700" />
        <input type="password" name="confirm_password" placeholder="Confirm Password" required class="w-full p-3 rounded border bg-gray-50 dark:bg-gray-700" />
        <select name="role" class="w-full p-3 rounded border bg-gray-50 dark:bg-gray-700 text-black dark:text-white">
          <option value="member" selected>Member</option>
          <option value="admin">Admin</option>
          <option value="chairman">Chairman</option>
        </select>
        <button type="submit" name="add_member" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded shadow">Add Member</button>
      </form>
    </section>

    <!-- Members List -->
    <section class="bg-white dark:bg-gray-800 rounded-lg shadow p-8">
      <h2 class="text-2xl font-bold mb-6">Your Members</h2>
      <?php if ($result->num_rows === 0): ?>
        <p class="text-gray-600 dark:text-gray-400">No members added yet.</p>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full table-auto border border-gray-300 dark:border-gray-600">
            <thead>
              <tr class="bg-indigo-100 dark:bg-indigo-700 text-indigo-900 dark:text-indigo-200">
                <th class="px-5 py-3">Name</th>
                <th class="px-5 py-3">Email</th>
                <th class="px-5 py-3">Role</th>
                <th class="px-5 py-3">Joined</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($member = $result->fetch_assoc()): ?>
                <tr>
                  <td class="border px-5 py-3"><?= htmlspecialchars($member['name']) ?></td>
                  <td class="border px-5 py-3"><?= htmlspecialchars($member['email']) ?></td>
                  <td class="border px-5 py-3 capitalize"><?= htmlspecialchars($member['role']) ?></td>
                  <td class="border px-5 py-3"><?= date('M d, Y', strtotime($member['created_at'])) ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>
  </main>
</div>

<script>
const darkToggle = document.getElementById('darkToggle');
const htmlEl = document.documentElement;
if(localStorage.getItem('darkMode') === 'enabled') {
  htmlEl.classList.add('dark');
  darkToggle.textContent = '☀️';
}
darkToggle.addEventListener('click', () => {
  htmlEl.classList.toggle('dark');
  const isDark = htmlEl.classList.contains('dark');
  localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
  darkToggle.textContent = isDark ? '☀️' : '🌙';
});

function updateClockAndCalendar() {
  const now = new Date();
  const h = now.getHours(), m = now.getMinutes().toString().padStart(2, '0'), s = now.getSeconds().toString().padStart(2, '0');
  const ampm = h >= 12 ? 'PM' : 'AM';
  const hour12 = h % 12 || 12;
  document.getElementById('clock').textContent = `${hour12}:${m}:${s} ${ampm}`;
  document.getElementById('calendar').textContent = now.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
}
setInterval(updateClockAndCalendar, 1000);
updateClockAndCalendar();
</script>
</body>
</html>
