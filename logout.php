<?php
session_start(); // เริ่มเซสชัน

// ทำลายเซสชันทั้งหมด
session_unset(); // ทำให้ตัวแปรเซสชันทั้งหมดเป็นค่า NULL
session_destroy(); // ทำลายเซสชัน

// เปลี่ยนเส้นทางไปยังหน้าหลักหรือหน้าเข้าสู่ระบบ
header("Location: index.php");
exit();
?>
