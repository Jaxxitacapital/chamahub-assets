<?php
require_once 'db_chamahub.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 30px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            margin-top: 0;
            text-align: center;
            color: #333;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        button {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }

            input[type="password"], button {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>

<div class="container">
<?php
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($email, $expires);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        if (strtotime($expires) > time()) {
            // Valid token — show form
            echo "
                <h2>Reset Your Password</h2>
                <form action='update-password.php' method='POST'>
                    <input type='hidden' name='email' value='$email'>
                    <input type='password' name='new_password' placeholder='Enter new password' required>
                    <button type='submit'>Reset Password</button>
                </form>
            ";
        } else {
            echo "<div class='alert alert-error'>This reset link has expired. Please request a new one.</div>";
        }
    } else {
        echo "<div class='alert alert-error'>Invalid reset token. Please try again.</div>";
    }

    $stmt->close();
} else {
    echo "<div class='alert alert-error'>No token provided in the URL.</div>";
}
?>
</div>

</body>
</html>
