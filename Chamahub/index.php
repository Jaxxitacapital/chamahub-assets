<?php
// index.php — Landing Page — No session or authentication required
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Welcome | ChamaHub</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: url('bck/1.jpg') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .overlay {
      background: rgba(0, 0, 0, 0.65);
      padding: 60px 40px;
      border-radius: 20px;
      text-align: center;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.5);
      backdrop-filter: blur(10px);
    }

    .overlay h1 {
      font-size: 2.8em;
      color: #ffffff;
      margin-bottom: 10px;
      text-shadow: 1px 1px 5px #007bff;
    }

    .overlay p {
      font-size: 1.2em;
      color: #d0d0d0;
      margin-bottom: 30px;
    }

    form input[type="email"],
    form input[type="password"] {
      width: 100%;
      padding: 12px 15px;
      margin: 10px 0;
      border: none;
      border-radius: 10px;
      font-size: 1em;
    }

    form button {
      background: linear-gradient(to right, #007bff, #00d4ff);
      color: white;
      padding: 14px 28px;
      border: none;
      border-radius: 50px;
      font-size: 1em;
      font-weight: 500;
      cursor: pointer;
      box-shadow: 0 5px 15px rgba(0,123,255,0.4);
      transition: all 0.3s ease-in-out;
      width: 100%;
      margin-top: 15px;
    }

    form button:hover {
      background: linear-gradient(to right, #0056b3, #00aaff);
      transform: scale(1.03);
      box-shadow: 0 10px 20px rgba(0,123,255,0.6);
    }

    .message {
      color: #ff5555;
      margin-bottom: 1rem;
      font-weight: 600;
      text-align: center;
    }

    .extras {
      margin-top: 20px;
      font-size: 0.95em;
    }

    .extras a {
      color: #00d4ff;
      text-decoration: none;
      margin: 0 10px;
    }

    .extras a:hover {
      text-decoration: underline;
    }

    @media (max-width: 600px) {
      .overlay {
        padding: 40px 20px;
      }

      .overlay h1 {
        font-size: 2em;
      }

      .overlay p {
        font-size: 1em;
      }
    }
  </style>
</head>
<body>
  <div class="overlay">
    <h1>Welcome to ChamaHub</h1>
    <p>A platform to manage your chama with ease.</p>

    <?php
    if (!empty($_SESSION['login_error'])) {
        echo '<p class="message">' . htmlspecialchars($_SESSION['login_error']) . '</p>';
        unset($_SESSION['login_error']);
    }
    ?>

    <form action="includes/login.php" method="POST">
      <input type="email" name="email" placeholder="Email address or Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="login">Login</button>
    </form>

    <div class="extras">
      <a href="register.php">Create account</a> |
      <a href="forgot-password.php">Forgot password?</a>
    </div>
  </div>
</body>
</html>
