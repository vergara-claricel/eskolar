<?php
http_response_code(403);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Access Denied | eSkolar</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f7fa;
            font-family: Arial, sans-serif;
            color: #2d3748;
        }

        .error-container {
            width: 90%;
            max-width: 450px;
            padding: 40px 30px;
            text-align: center;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .error-code {
            font-size: 64px;
            font-weight: 700;
            color: #2d5f8b;
            margin-bottom: 10px;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 24px;
        }

        p {
            margin: 0 0 25px;
            font-size: 14px;
            line-height: 1.6;
            color: #718096;
        }

        .login-btn {
            display: inline-block;
            padding: 11px 22px;
            background: #2d5f8b;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        .login-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>

    <div class="error-container">

        <div class="error-code">403</div>

        <h1>Access Denied</h1>

        <p>
            You don't have permission to access this page.
            Please log in to continue.
        </p>

        <a href="/esko/login.php" class="login-btn">
            Return to Login
        </a>

    </div>

</body>
</html>