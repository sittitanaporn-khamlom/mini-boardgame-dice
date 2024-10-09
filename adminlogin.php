<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account</title>
    <link rel="stylesheet" href="css/adminlogin.css"> <!-- เปลี่ยนเส้นทางตามที่คุณต้องการ -->
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2>STAFF Account</h2>
            <form action="" method="post"> <!-- ใช้ action เป็น empty string -->
                <div class="input-group">
                    <label for="stID">STAFF ID</label>
                    <input type="text" id="stID" name="stID" required>
                </div>
                <div class="input-group">
                    <label for="stPass">Password</label>
                    <input type="password" id="stPass" name="stPass" required>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
            <a href="index.php" class="back-btn">Back</a> <!-- ปุ่ม Back -->
        </div>
    </div>

    <?php
    session_start(); // เริ่ม session

    // เชื่อมต่อฐานข้อมูล
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $servername = "localhost"; // เปลี่ยนตามการตั้งค่าของคุณ
        $username = "root"; // เปลี่ยนตามการตั้งค่าของคุณ
        $password = ""; // เปลี่ยนตามการตั้งค่าของคุณ
        $dbname = "bgmini"; // ชื่อฐานข้อมูลของคุณ

        // สร้างการเชื่อมต่อ
        $conn = new mysqli($servername, $username, $password, $dbname);

        // ตรวจสอบการเชื่อมต่อ
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // รับค่าจากฟอร์ม
        $stID = $_POST['stID'];
        $stPass = $_POST['stPass'];

        // ตรวจสอบข้อมูลในฐานข้อมูล
        $sql = "SELECT * FROM bgstaff WHERE stID = ? AND stPass = ?"; // แก้ไขชื่อ table ให้ตรงกับฐานข้อมูลของคุณ
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $stID, $stPass);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // ถ้าข้อมูลถูกต้อง
            $_SESSION['stID'] = $stID; // เก็บค่าผู้ใช้ใน session
            header("Location: systemmanage.php");
            exit();
        } else {
            // ถ้าข้อมูลไม่ถูกต้อง
            echo "<script>alert('Invalid STAFF ID or password'); window.location.href='adminlogin.php';</script>";
        }

        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>
