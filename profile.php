<?php
session_start(); // เริ่มเซสชัน
require 'DBcon.php'; // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['memid'])) {
    header("Location: index.php"); // หากยังไม่ได้ล็อกอิน ให้เปลี่ยนเส้นทางไปที่หน้าเข้าสู่ระบบ
    exit();
}

// รับ ID ของผู้ใช้ที่ล็อกอิน
$memid = $_SESSION['memid'];

// ดึงข้อมูลผู้ใช้
$user_query = "SELECT * FROM bgmem WHERE memid='$memid'";
$user_result = $conn->query($user_query);

if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
} else {
    echo "เกิดข้อผิดพลาดในการดึงข้อมูลผู้ใช้.";
}

// อัปเดตข้อมูลผู้ใช้
if (isset($_POST['update_profile'])) {
    $memname = $_POST['memname'];
    $memlast = $_POST['memlast'];
    $memage = $_POST['memage'];
    $memaddress = $_POST['memaddress'];

    // อัปโหลดรูปภาพ
    if (isset($_FILES['memimg']) && $_FILES['memimg']['error'] == 0) {
        $memimg = file_get_contents($_FILES['memimg']['tmp_name']);
        $memimg = base64_encode($memimg); // แปลงรูปภาพเป็น base64
    } else {
        $memimg = $user_data['memimg']; // ถ้าไม่อัปโหลดรูปใหม่ ให้ใช้รูปเดิม
    }

    $update_query = "UPDATE bgmem SET memname='$memname', memlast='$memlast', memage='$memage', memaddress='$memaddress', memimg='$memimg' WHERE memid='$memid'";

    if ($conn->query($update_query) === TRUE) {
        echo "<div id='message'>อัปเดตข้อมูลสำเร็จ!</div>";
        echo "<script>
                setTimeout(function() {
                    document.getElementById('message').style.display='none';
                    window.location.href = 'mainmenu.php';
                }, 1000); // หายไปหลังจาก 1 วินาที
              </script>";
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $conn->error;
    }
}

// ลบบัญชีผู้ใช้
if (isset($_POST['delete_account'])) {
    $delete_query = "DELETE FROM bgmem WHERE memid='$memid'";
    if ($conn->query($delete_query) === TRUE) {
        session_destroy(); // ทำลายเซสชัน
        header("Location: index.php");
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการลบบัญชี: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profiles.css">
    <title>Profile</title>
    <style>
        #message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="main-content">
        <h2>Profile</h2>

        <div class="profile-image">
            <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($user_data['memimg'] ?: base64_encode(file_get_contents('img/all.png'))); ?>" alt="Profile Image" style="object-fit: cover;">
        </div>

        <div class="user-info">
            <p><strong>Username :</strong> <?php echo htmlspecialchars($user_data['memid']); ?></p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <label for="memname">Firstname:</label>
            <input type="text" name="memname" id="memname"
                value="<?php echo htmlspecialchars($user_data['memname']); ?>" required>

            <label for="memlast">Lastname:</label>
            <input type="text" name="memlast" id="memlast"
                value="<?php echo htmlspecialchars($user_data['memlast']); ?>" required>

            <label for="memage">Age:</label>
            <input type="text" name="memage" id="memage" value="<?php echo htmlspecialchars($user_data['memage']); ?>"
                required>

            <label for="memaddress">Address:</label>
            <input type="text" name="memaddress" id="memaddress"
                value="<?php echo htmlspecialchars($user_data['memaddress']); ?>" required>

            <label for="memimg">EditProfile:</label>
            <input type="file" name="memimg" id="memimg">

            <input type="submit" name="update_profile" value="Update Profile">
        </form>

        <form method="POST" onsubmit="return confirm('Do you want to delete your account??');">
            <input type="submit" name="delete_account" value="delete your account ">
        </form>

        <a href="mainmenu.php" class="back-button">Back to Menu</a>
    </div>
</body>

</html>