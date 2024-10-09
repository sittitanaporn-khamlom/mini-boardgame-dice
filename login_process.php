<?php
session_start(); // เริ่มเซสชัน

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bgmini";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $memid = $_POST['username'];
    $mempass = $_POST['password'];

    // ตรวจสอบข้อมูลในฐานข้อมูล
    $sql = "SELECT * FROM bgmem WHERE memid = ? AND mempass = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $memid, $mempass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ถ้าข้อมูลถูกต้อง
        $row = $result->fetch_assoc();
        $_SESSION['memid'] = $row['memid']; // เก็บ memid ในเซสชัน
        $_SESSION['login_success'] = "Login successful!";
    } else {
        // ถ้าข้อมูลไม่ถูกต้อง
        $_SESSION['login_error'] = "Invalid username or password.";
    }

    $stmt->close();
}

$conn->close();

// ส่งกลับไปที่หน้า index.php
header("Location: index.php");
exit();
?>
