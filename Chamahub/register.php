<?php
// register.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once(__DIR__ . '/includes/db_chamahub.php');

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

$msg = "";
$msg_type = "error"; // can be 'error' or 'success'

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $chama_name = trim($_POST['chama_name']);

    // Role fixed to 'chairman'
    $role = 'chairman';

    if ($password !== $confirm_password) {
        $msg = "Passwords do not match.";
    } elseif (empty($chama_name)) {
        $msg = "Please enter your Chama name.";
    } elseif (strlen($password) < 8) {
        $msg = "Password must be at least 8 characters.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $msg = "Username or Email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, name, email, role, chama_name, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssssss", $username, $hashed_password, $name, $email, $role, $chama_name);

            if ($stmt->execute()) {
                $msg = "Registration successful! Redirecting to dashboard...";
                $msg_type = "success";
                $_SESSION['user'] = $username;
                // Delay redirect to show success message in popup
                header("refresh:3;url=dashboard.php");
            } else {
                $msg = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth" >
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Register Chairman - ChamaHub</title>

<!-- Tailwind CDN for quick styling -->
<script src="https://cdn.tailwindcss.com"></script>

<style>
  body {
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
  }

  /* Modal backdrop */
  #modalBackdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 32, 39, 0.8);
    z-index: 50;
    backdrop-filter: blur(4px);
  }

  /* Modal box */
  #modal {
    background: white;
    max-width: 90vw;
    max-height: 40vh;
    margin: auto;
    padding: 1.5rem 2rem;
    border-radius: 0.75rem;
    box-shadow: 0 20px 25px rgba(0, 0, 0, 0.2);
    color: #1e293b; /* slate-800 */
    font-weight: 600;
    text-align: center;
    position: relative;
    top: 25vh;
  }

  #modal.success {
    border-left: 6px solid #22c55e; /* green-500 */
  }
  #modal.error {
    border-left: 6px solid #ef4444; /* red-500 */
  }

  #modal button.close-btn {
    position: absolute;
    top: 0.5rem;
    right: 0.75rem;
    background: transparent;
    border: none;
    font-size: 1.5rem;
    font-weight: 700;
    cursor: pointer;
    color: #64748b; /* slate-500 */
    transition: color 0.2s ease;
  }

  #modal button.close-btn:hover {
    color: #1e293b; /* slate-800 */
  }
</style>

</head>
<body class="min-h-screen flex items-center justify-center">

<div class="register-container bg-white bg-opacity-10 backdrop-blur-md p-8 rounded-xl max-w-md w-full mx-4 shadow-xl">

  <h2 class="text-3xl text-white font-extrabold mb-8 text-center select-none">Create Chairman Account</h2>

  <form method="POST" autocomplete="off" class="space-y-5">

    <input type="text" name="username" placeholder="Username" required
           class="w-full p-3 rounded border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />

    <input type="text" name="name" placeholder="Full Name" required
           class="w-full p-3 rounded border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />

    <input type="text" name="chama_name" placeholder="Chama Name" required
           class="w-full p-3 rounded border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />

    <input type="email" name="email" placeholder="Email Address" required
           class="w-full p-3 rounded border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none" />

    <!-- Password Input with Show/Hide toggle -->
    <div class="relative">
      <input
        id="password"
        type="password"
        name="password"
        placeholder="Password (min 8 chars)"
        required
        class="w-full p-3 rounded border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none pr-12"
        oninput="checkPasswordStrength()"
      />
      <button type="button" onclick="togglePassword('password')" 
        class="absolute right-3 top-1/2 -translate-y-1/2 text-indigo-600 font-semibold hover:text-indigo-800 focus:outline-none select-none"
        tabindex="-1"
        aria-label="Toggle password visibility"
      >Show</button>
    </div>

    <!-- Confirm Password Input with Show/Hide toggle -->
    <div class="relative">
      <input
        id="confirm_password"
        type="password"
        name="confirm_password"
        placeholder="Confirm Password"
        required
        class="w-full p-3 rounded border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none pr-12"
      />
      <button type="button" onclick="togglePassword('confirm_password')" 
        class="absolute right-3 top-1/2 -translate-y-1/2 text-indigo-600 font-semibold hover:text-indigo-800 focus:outline-none select-none"
        tabindex="-1"
        aria-label="Toggle password visibility"
      >Show</button>
    </div>

    <!-- Password Strength Meter -->
    <div class="h-2 rounded bg-gray-300 overflow-hidden">
      <div id="strengthBar" class="h-full w-0 transition-all duration-300 ease-in-out"></div>
    </div>
    <p id="strengthText" class="mt-1 text-sm font-semibold text-gray-300 select-none"></p>

    <button type="submit" name="register"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded transition">
      Register
    </button>

  </form>
</div>

<!-- Modal Popup -->
<div id="modalBackdrop" role="dialog" aria-modal="true" aria-labelledby="modalTitle" tabindex="-1">
  <div id="modal" class="<?= $msg_type ?>">
    <button class="close-btn" aria-label="Close message" onclick="closeModal()">&times;</button>
    <p id="modalMessage"><?= htmlspecialchars($msg) ?></p>
  </div>
</div>

<script>
  // Toggle password visibility
  function togglePassword(id) {
    const input = document.getElementById(id);
    const btn = input.nextElementSibling;
    if (input.type === 'password') {
      input.type = 'text';
      btn.textContent = 'Hide';
    } else {
      input.type = 'password';
      btn.textContent = 'Show';
    }
  }

  // Password strength checker
  function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');

    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[\W_]/)) strength++;

    switch (strength) {
      case 0:
      case 1:
        strengthBar.style.width = '20%';
        strengthBar.style.backgroundColor = '#ef4444'; // red-500
        strengthText.textContent = 'Very Weak';
        break;
      case 2:
        strengthBar.style.width = '40%';
        strengthBar.style.backgroundColor = '#f97316'; // orange-500
        strengthText.textContent = 'Weak';
        break;
      case 3:
        strengthBar.style.width = '60%';
        strengthBar.style.backgroundColor = '#eab308'; // yellow-500
        strengthText.textContent = 'Medium';
        break;
      case 4:
        strengthBar.style.width = '80%';
        strengthBar.style.backgroundColor = '#22c55e'; // green-500
        strengthText.textContent = 'Strong';
        break;
      case 5:
        strengthBar.style.width = '100%';
        strengthBar.style.backgroundColor = '#16a34a'; // green-700
        strengthText.textContent = 'Very Strong';
        break;
    }
  }

  // Modal control
  const modalBackdrop = document.getElementById('modalBackdrop');
  const modal = document.getElementById('modal');

  function closeModal() {
    modalBackdrop.style.display = 'none';
  }

  // Show modal if there is a message
  <?php if (!empty($msg)): ?>
  modalBackdrop.style.display = 'block';

  // Auto close after 5 seconds (only for success messages)
  <?php if ($msg_type === 'success'): ?>
    setTimeout(() => {
      closeModal();
    }, 5000);
  <?php endif; ?>
  <?php endif; ?>
</script>

</body>
</html>
