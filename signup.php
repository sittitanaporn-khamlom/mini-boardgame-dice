<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Boardgame Dice</title>
    <link rel="stylesheet" href="css/signup.css">
    <style>
        /* Style for back button */
        .back-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #FF6922;
            /* Secondary Color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background-color: #e65c1c;
            /* Darker shade for hover effect */
        }

        /* Align password requirements text to the left */
        .password-requirements {
            margin-top: 5px;
            text-align: left;
            /* Aligns text to the left */
        }

        /* Position show password toggle to the right */
        .input-group {
            position: relative;
            /* Set relative positioning for input group */
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            /* Adjust as necessary for spacing */
            top: 50%;
            /* Center vertically */
            transform: translateY(-50%);
            /* Adjust vertical alignment */
            cursor: pointer;
            padding: 0 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="signup-box">
            <h2>Create an Account</h2>
            <form action="" method="post" onsubmit="return validatePassword()">
                <!-- กล่องข้อความแสดงแจ้งเตือน -->
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "bgmini";

                    // เชื่อมต่อฐานข้อมูล
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $memname = $_POST['memname'];
                    $memlast = $_POST['memlast'];
                    $memid = $_POST['memid'];
                    $mempass = $_POST['mempass'];
                    $memage = $_POST['memage'];
                    $memaddress = $_POST['memaddress'];

                    // ตรวจสอบว่า username ซ้ำหรือไม่
                    $sql_check = "SELECT * FROM bgmem WHERE memid = ?";
                    $stmt_check = $conn->prepare($sql_check);
                    $stmt_check->bind_param("s", $memid);
                    $stmt_check->execute();
                    $result_check = $stmt_check->get_result();

                    if ($result_check->num_rows > 0) {
                        $error_message = "Username already exists. Please choose another username.";
                        echo "<script>
                                setTimeout(function() {
                                    document.querySelector('.message-box.error').style.display = 'none';
                                }, 5000); // ซ่อนข้อความหลังจาก 5 วินาที
                            </script>";
                    } else {
                        $sql_insert = "INSERT INTO bgmem (memname, memlast, memid, mempass, memage, memaddress) VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt_insert = $conn->prepare($sql_insert);
                        $stmt_insert->bind_param("ssssis", $memname, $memlast, $memid, $mempass, $memage, $memaddress);

                        if ($stmt_insert->execute()) {
                            $success_message = "New account created successfully.";
                            echo "<script>
                                    setTimeout(function() {
                                        window.location.href = 'index.php';
                                    }, 1000); // กลับไปหน้า index.php หลังจาก 1 วินาที
                                </script>";
                        } else {
                            $error_message = "Error: " . $conn->error;
                            echo "<script>
                                    setTimeout(function() {
                                        document.querySelector('.message-box.error').style.display = 'none';
                                    }, 3000); // ซ่อนข้อความหลังจาก 5 วินาที
                                </script>";
                        }

                        $stmt_insert->close();
                    }

                    $stmt_check->close();
                    $conn->close();
                }

                // แสดงข้อความแจ้งเตือน
                if (isset($error_message)) {
                    echo "<div class='message-box error'>$error_message</div>";
                } elseif (isset($success_message)) {
                    echo "<div class='message-box success'>$success_message</div>";
                }
                ?>
                <div class="input-group">
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="memname" required>
                </div>
                <div class="input-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="memlast" required>
                </div>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="memid" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="mempass" required>
                    <div class="toggle-password" onclick="togglePasswordVisibility()">🔒 Show</div>
                    <div class="password-requirements">
                        <p>รหัสผ่านต้องมี:</p>
                        <ul>
                            <li>ความยาวอย่างน้อย 8 ตัวอักษร</li>
                            <li>ตัวพิมพ์ใหญ่อย่างน้อย 1 ตัว</li>
                            <li>ตัวพิมพ์เล็กอย่างน้อย 1 ตัว</li>
                            <li>ตัวเลขอย่างน้อย 1 ตัว</li>
                            <li>อักขระพิเศษ (เช่น @, #, $, %, &, *)</li>
                        </ul>
                    </div>
                </div>
                <div class="input-group">
                    <label for="age">Age</label>
                    <input type="text" id="age" name="memage" required>
                </div>
                <div class="input-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="memaddress" required>
                </div>
                <button type="submit" class="signup-btn">Sign Up</button>
                <a href="index.php" class="back-btn">Back</a>
            </form>

        </div>
    </div>

    <script>
        function validatePassword() {
            const password = document.getElementById('password').value;
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /\d/.test(password),
                special: /[!@#$%^&*()\-_=+{}[\]|\\:;\"'<>,.?/~`]/.test(password),
            };

            let errorMessage = "รหัสผ่านไม่ตรงตามความต้องการ กรุณาตรวจสอบเงื่อนไขต่อไปนี้:\n";
            let isValid = true;

            if (!requirements.length) {
                errorMessage += "- ต้องมีความยาวอย่างน้อย 8 ตัวอักษร\n";
                isValid = false;
            }
            if (!requirements.uppercase) {
                errorMessage += "- ต้องมีตัวพิมพ์ใหญ่อย่างน้อย 1 ตัว\n";
                isValid = false;
            }
            if (!requirements.lowercase) {
                errorMessage += "- ต้องมีตัวพิมพ์เล็กอย่างน้อย 1 ตัว\n";
                isValid = false;
            }
            if (!requirements.number) {
                errorMessage += "- ต้องมีตัวเลขอย่างน้อย 1 ตัว\n";
                isValid = false;
            }
            if (!requirements.special) {
                errorMessage += "- ต้องมีอักขระพิเศษ (เช่น @, #, $, %, &, *)\n";
                isValid = false;
            }

            if (!isValid) {
                alert(errorMessage);
                return false; // ห้ามส่งแบบฟอร์ม
            }
            return true; // อนุญาตให้ส่งแบบฟอร์ม
        }

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