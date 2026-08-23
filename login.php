<?php
require_once '../esko/classes/supa_login.php';
session_start();

// if (isset($_POST['login'])) {

//     $username = trim($_POST['username']) ?? '';
//     $password = trim($_POST['password']) ?? '';

//     $apiKey = "sb_publishable_JVZTWW0Ld8XJ_HQz7LGXkA_iLUkpN7c";
//     $url = "https://yaksrweqnidfpvceoqzo.supabase.co/rest/v1/users"
//          . "?username=eq." . urlencode($username)
//          . "&select=id,role,password";

//     $ch = curl_init($url);

//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, [
//         "apikey: $apiKey",
//         "Authorization: Bearer $apiKey"
//     ]);

//     $response = curl_exec($ch);
//     curl_close($ch);

//     $users = json_decode($response, true);

//     $user = $users[0] ?? null;

//     if ($user && password_verify($password, $user['password'])) {
//         $_SESSION["userid"] = $user["id"];
//         $_SESSION["role"] = $user["role"];

//         // role-based redirect
//         if ($user["role"] === "admin") {
//             header("Location: /esko/admin/admin_dashboard.php");
//         }
//         exit;

//     } else {
//         $error = "Invalid username or password";
//     }
// }

$userobj = new User($api);

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $user = $userobj->login($username, $password);
    if ($user) {
        $_SESSION["userid"] = $user["id"];
        $_SESSION["role"] = $user["role"];
        if ($user["role"] === "admin") {
            header("Location: /esko/admin/admin_dashboard.php");
            exit;
        }
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
    <title>eSkolar | Administration</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f5f7f4;
            color: #254725;
        }

        .login-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
        }

        /* =========================
           LEFT BRANDING SECTION
        ========================= */

        .branding {
            position: relative;
            background: #254725;
            color: white;
            padding: 60px;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .branding-content {
            position: relative;
            z-index: 2;
            max-width: 520px;
            margin: auto;
        }

.brand-mark {
    width: 72px;
    height: 72px;
    border-radius: 14px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 28px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
}

.brand-mark img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 9px;
}

.partner-logos {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-top: 30px;
}

.partner-logos img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    background: #ffffff;
    border-radius: 50px;
    padding: 5px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
}

        .branding h1 {
            font-size: clamp(38px, 5vw, 58px);
            line-height: 1;
            letter-spacing: -2px;
            margin-bottom: 20px;
            font-weight: 750;
        }

        .branding h1 span {
            color: #FFC857;
        }

        .branding-description {
            color: #cbd8cc;
            font-size: 16px;
            line-height: 1.7;
            max-width: 430px;
            margin-bottom: 38px;
        }

        /* Decorative elements */

        .decor-circle {
            position: absolute;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        .circle-one {
            width: 500px;
            height: 500px;
            right: -250px;
            top: -180px;
        }

        .circle-two {
            width: 350px;
            height: 350px;
            left: -200px;
            bottom: -180px;
        }

        .decor-line {
            position: absolute;
            width: 200px;
            height: 1px;
            background: rgba(255,255,255,0.1);
            transform: rotate(-35deg);
            bottom: 100px;
            right: 80px;
        }


        /* =========================
           RIGHT LOGIN SECTION
        ========================= */

        .login-section {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #f5f7f4;
        }

        .login-wrapper {
            width: 100%;
            max-width: 390px;
        }

        .login-header {
            margin-bottom: 32px;
        }

        .login-header h2 {
            color: #254725;
            font-size: 30px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #718071;
            font-size: 14px;
            line-height: 1.5;
        }


        /* Error */

        .error {
            display: flex;
            align-items: center;
            gap: 10px;

            background: #fff8e8;
            border: 1px solid #f2d58d;
            color: #775914;

            padding: 12px 14px;
            border-radius: 8px;

            font-size: 13px;
            margin-bottom: 20px;
        }


        /* Form */

        .login-form {
            background: white;
            border: 1px solid #e1e8e1;
            border-radius: 12px;
            padding: 28px;

            box-shadow:
                0 8px 30px rgba(37, 71, 37, 0.06);
        }

        .input-group {
            margin-bottom: 19px;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #344b34;
            margin-bottom: 7px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            height: 46px;

            border: 1px solid #d3ddd4;
            border-radius: 7px;

            background: #fbfcfb;

            padding: 0 42px 0 13px;

            font-size: 14px;
            color: #254725;

            outline: none;
            transition: 0.2s ease;
        }

        .input-wrapper input::placeholder {
            color: #a5afa6;
        }

        .input-wrapper input:focus {
            background: white;
            border-color: #4C7742;
            box-shadow: 0 0 0 3px rgba(76, 119, 66, 0.10);
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);

            border: none;
            background: transparent;

            color: #89958b;
            cursor: pointer;

            font-size: 12px;
            padding: 4px;
        }

        .password-toggle:hover {
            color: #254725;
        }


        /* Login button */

        .login-btn {
            width: 100%;
            height: 46px;

            border: none;
            border-radius: 7px;

            background: #4C7742;
            color: white;

            font-size: 14px;
            font-weight: 600;

            cursor: pointer;
            transition: 0.2s ease;

            margin-top: 5px;
        }

        .login-btn:hover {
            background: #254725;
        }

        .login-btn:active {
            transform: translateY(1px);
        }


        /* Footer */

        .login-footer {
            text-align: center;
            margin-top: 22px;

            color: #9aa49b;
            font-size: 11px;
        }


        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 850px) {

            .login-page {
                grid-template-columns: 1fr;
            }

            .branding {
                display: none;
            }

            .login-section {
                min-height: 100vh;
                padding: 25px;
            }

            .login-wrapper {
                max-width: 400px;
            }
        }

        @media (max-width: 420px) {

            .login-section {
                padding: 20px;
            }

            .login-form {
                padding: 22px;
            }

            .login-header h2 {
                font-size: 27px;
            }
        }

        /* =========================
   PAGE ANIMATIONS
========================= */

/* Initial page fade */
.login-page {
    animation: pageFadeIn 0.7s ease-out;
}

@keyframes pageFadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}


/* Branding content */
.branding-content {
    animation: brandingIn 0.8s ease-out;
}

@keyframes brandingIn {
    from {
        opacity: 0;
        transform: translateX(-35px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }
}


/* Logo */
.brand-mark {
    animation: logoIn 0.7s ease-out 0.1s both;
}

@keyframes logoIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}


/* Title */
.branding h1 {
    animation: titleIn 0.7s ease-out 0.2s both;
}

@keyframes titleIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}


/* Description */
.branding-description {
    animation: textIn 0.7s ease-out 0.3s both;
}

@keyframes textIn {
    from {
        opacity: 0;
        transform: translateY(12px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}


/* Partner logos */
.partner-logos {
    animation: logosIn 0.7s ease-out 0.4s both;
}

@keyframes logosIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}


/* Login side */
.login-wrapper {
    animation: loginIn 0.7s ease-out 0.15s both;
}

@keyframes loginIn {
    from {
        opacity: 0;
        transform: translateX(30px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }
}


/* Login button */
.login-btn {
    position: relative;
    overflow: hidden;
}

.login-btn::after {
    content: "";

    position: absolute;
    top: 0;
    left: -100%;

    width: 100%;
    height: 100%;

    background: linear-gradient(
        90deg,
        transparent,
        rgba(255,255,255,0.15),
        transparent
    );

    transition: left 0.5s ease;
}

.login-btn:hover::after {
    left: 100%;
}


/* Partner logo hover */
.partner-logos img {
    transition: transform 0.25s ease;
}

.partner-logos img:hover {
    transform: translateY(-4px) scale(1.05);
}


/* Branding decorative circles */
.circle-one {
    animation: circleFloat 8s ease-in-out infinite;
}

.circle-two {
    animation: circleFloatReverse 10s ease-in-out infinite;
}

@keyframes circleFloat {
    0%, 100% {
        transform: translate(0, 0);
    }

    50% {
        transform: translate(-15px, 12px);
    }
}

@keyframes circleFloatReverse {
    0%, 100% {
        transform: translate(0, 0);
    }

    50% {
        transform: translate(12px, -10px);
    }
}


/* Accessibility */
@media (prefers-reduced-motion: reduce) {

    *,
    *::before,
    *::after {
        animation: none !important;
        transition: none !important;
    }

}
    </style>
</head>

<body>

<div class="login-page">

    <section class="branding">

        <div class="decor-circle circle-one"></div>
        <div class="decor-circle circle-two"></div>
        <div class="decor-line"></div>

        <div class="branding-content">

            <div class="brand-mark">
                <img src="assets/images/eskolar_logo.png" alt="eskolar logo">
            </div>
            <h1>
                e<span>Skolar</span>
            </h1>

            <p class="branding-description">
                An integrated and centralized system for Iskolar ng Bagac designed
                to simplify scholar records, activities, and attendance monitoring.
            </p>
            <div class="partner-logos">
    <img src="assets/images/logo_iskolar.jpg" alt="Iskolar ng Bagac">
    <img src="assets/images/bagac_logo.png" alt="Municipality of Bagac">
</div>
        </div>

    </section>


    <main class="login-section">

        <div class="login-wrapper">

            <div class="login-header">
                <h2>Welcome back!</h2>
                <p>
                    Sign in to access the eSkolar administration portal.
                </p>

            </div>


            <?php if (!empty($error)): ?>

                <div class="error">
                    <span>⚠</span>

                    <span>
                        <?= htmlspecialchars($error) ?>
                    </span>
                </div>

            <?php endif; ?>


            <form method="POST" class="login-form">

                <div class="input-group">

                    <label for="username">
                        Username
                    </label>

                    <div class="input-wrapper">

                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Enter your username"
                            autocomplete="username"
                            required
                        >

                    </div>

                </div>


                <div class="input-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="input-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="togglePassword"
                        >
                            Show
                        </button>

                    </div>

                </div>


                <button
                    type="submit"
                    name="login"
                    class="login-btn"
                >
                    Sign in
                </button>

            </form>


            <div class="login-footer">
                eSkolar Administration Portal
            </div>

        </div>

    </main>

</div>


<script>

const password = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");

togglePassword.addEventListener("click", function () {

    const isPassword = password.type === "password";

    password.type = isPassword ? "text" : "password";

    togglePassword.innerText = isPassword ? "Hide" : "Show";

});

</script>

</body>
</html>