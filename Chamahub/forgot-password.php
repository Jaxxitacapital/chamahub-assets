<?php
// forgot-password.php
session_start();
require_once(__DIR__ . '/includes/db_chamahub.php'); // Ensure db_chamahub.php defines $conn
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - ChamaHub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Jaxxita-Inspired Styling -->
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="email"] {
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: none;
            border-radius: 0.5rem;
            outline: none;
            background: #f3f3f3;
            color: #333;
        }

        button {
            padding: 0.9rem;
            background: #00c6ff;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #0072ff;
        }

        .message {
            text-align: center;
            margin-bottom: 1rem;
            padding: 0.6rem;
            border-radius: 0.4rem;
            background-color: rgba(255, 255, 255, 0.15);
        }

        @media (max-width: 480px) {
            .container {
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>

        <?php if (isset($_SESSION['msg'])): ?>
            <div class="message"><?= htmlspecialchars($_SESSION['msg']) ?></div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <form method="POST" action="send-reset.php">
            <input type="email" name="email" placeholder="Enter your email" required />
            <button type="submit">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
