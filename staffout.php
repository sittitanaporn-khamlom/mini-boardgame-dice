<?php
session_start(); // เริ่ม session
session_unset(); // ล้างค่าที่เก็บใน session
session_destroy(); // ทำลาย session

header("Location: index.php"); // เปลี่ยนเส้นทางไปยังหน้า index.php
exit();
?>
