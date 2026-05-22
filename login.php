<?php
session_start();
// require __DIR__ . "/connection.php"; // your DB connection
require __DIR__ . "/localcon.php";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT id, username, password, role FROM users WHERE username = :username LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password == $user["password"]) {
        $_SESSION["userid"] = $user["id"];
        $_SESSION["role"] = $user["admin"];
        header("Location: /esko/admin/admin_dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eSkolar Login</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f0f6ff;
            overflow: hidden;
        }

        /* Fade-in animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container {
            width: 360px;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 18px rgba(0,0,0,0.12);
            animation: fadeInUp 0.6s ease-out;
            text-align: center;
        }

        .logo {
            width: 85px;
            margin-bottom: 15px;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
        }

        h2 {
            margin-bottom: 20px;
            color: #1e4da8;
            font-weight: 700;
        }

        .input-group {
            text-align: left;
            margin-bottom: 15px;
        }

        label {
            font-size: 14px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 4px;
            border-radius: 6px;
            border: 1px solid #c5d2e8;
            outline: none;
            font-size: 15px;
        }

        input:focus {
            border-color: #1e4da8;
        }

        .btn {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            background: #1e4da8;
            border: none;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.2s;
            font-size: 16px;
        }

        .btn:hover {
            background: #163d85;
        }

        .error {
            background: #ffdddd;
            color: #b30000;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        /* Subtle floating background shape */
        .bg-shape {
            position: absolute;
            width: 500px;
            height: 500px;
            background: #1e4da8;
            border-radius: 50%;
            top: -150px;
            right: -150px;
            opacity: 0.15;
            filter: blur(5px);
        }
    </style>

</head>
<body>

<div class="bg-shape"></div>

<div class="login-container">
    <img src="assets/logo.png" alt="eSkolar Logo" class="logo">

    <h2>Welcome to eSkolar</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" name="login" class="btn">Login</button>
    </form>
</div>

</body>
</html>