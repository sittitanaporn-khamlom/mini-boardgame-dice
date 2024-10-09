<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Boardgame Dice</title>
    <link rel="stylesheet" href="css/index.css">
    <style>
        .message-box {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            color: #00B98E;
            background-color: #EFFDF5;
        }

        .error {
            color: #FF0000;
            background-color: #FDD;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-box">
            <div class="logo">
                <h2>YourLogo</h2>
            </div>
            <h1>Hello, <span class="highlight">Welcome to Boardgame Dice</span></h1>

            <!-- แสดงกล่องข้อความแจ้งเตือน -->
            <?php
            session_start();
            if (isset($_SESSION['login_error'])) {
                echo "<div class='message-box error'>{$_SESSION['login_error']}</div>";
                unset($_SESSION['login_error']); // ลบข้อความหลังแสดง
                // ซ่อนข้อความหลัง 10 วินาที
                echo "<script>
                        setTimeout(function() {
                            document.querySelector('.error').style.display = 'none';
                        }, 3000);
                      </script>";
            } elseif (isset($_SESSION['login_success'])) {
                echo "<div class='message-box success'>{$_SESSION['login_success']}</div>";
                unset($_SESSION['login_success']); // ลบข้อความหลังแสดง
                // Redirect หลังจาก 1 วินาที
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'mainmenu.php';
                        }, 1000);
                      </script>";
            }
            ?>

            <form action="login_process.php" method="post">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                    <div class="toggle-password" onclick="togglePasswordVisibility()">🔒 Show</div>
                    <div class="password-requirements"></div>
                </div>
                <div class="buttons">
                    <button type="submit" class="login-btn">Login</button>
                </div>
                <div class="buttons">
                    <a href="signup.php" class="signup-btn">Sign up</a>
                </div>
            </form>
            <div class="admin-link">
                <a href="adminlogin.php" class="admin-btn">STAFF</a>
            </div>
        </div>
        <div class="image-box"></div>
    </div>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleText = document.querySelector('.toggle-password');
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleText.innerHTML = "🔓 Hide";
            } else {
                passwordInput.type = "password";
                toggleText.innerHTML = "🔒 Show";
            }
        }
    </script>
</body>

</html>