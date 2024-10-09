<?php
// เชื่อมต่อฐานข้อมูล
require 'DBcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ตรวจสอบว่ามีข้อมูลจากฟอร์มหรือไม่
    if (isset($_POST['bgname']) && isset($_POST['bgdescript']) && 
        isset($_POST['bgplayer']) && isset($_POST['bgrule']) && 
        isset($_POST['bgdiff']) && isset($_POST['bgpublic']) && 
        isset($_POST['bgprice']) && isset($_POST['ref_bgtypeid']) && 
        isset($_POST['ref_bgclassid']) && isset($_FILES['bgimg'])) {

        // เริ่มต้น bgid
        $initialBgid = 10;

        // ค้นหาค่าสูงสุดของ bgid ในฐานข้อมูล
        $result = $conn->query("SELECT MAX(bgid) AS max_bgid FROM bgmanage");
        $row = $result->fetch_assoc();
        $maxBgid = $row['max_bgid'];

        // ตรวจสอบว่ามีข้อมูลในตาราง bgmanage หรือไม่
        if ($maxBgid) {
            // ถ้ามีข้อมูลให้เพิ่ม bgid ทีละ 1
            $bgid = $maxBgid + 1;
        } else {
            // ถ้าไม่มีข้อมูลให้เริ่มต้นที่ 10
            $bgid = $initialBgid;
        }

        // รับค่าจากฟอร์ม
        $bgname = $_POST['bgname'];
        $bgdescript = $_POST['bgdescript'];
        $bgplayer = $_POST['bgplayer'];
        $bgrule = $_POST['bgrule'];
        $bgdiff = $_POST['bgdiff'];
        $bgpublic = $_POST['bgpublic'];
        $bgprice = $_POST['bgprice'];
        $ref_bgtypeid = $_POST['ref_bgtypeid'];
        $ref_bgclassid = $_POST['ref_bgclassid'];

        // ประมวลผลรูปภาพ
        $bgimg = $_FILES['bgimg']['tmp_name'];
        $imgContent = addslashes(file_get_contents($bgimg)); // แปลงไฟล์รูปภาพเป็น binary

        // เตรียมคำสั่ง SQL สำหรับเพิ่มข้อมูลลงในฐานข้อมูล
        $sql = "INSERT INTO bgmanage (bgid, bgname, bgdescript, bgdiff, bgplayer, bgrule, bgpublic, bgprice, bgimg, ref_bgtypeid, ref_bgclassid)
                VALUES ('$bgid', '$bgname', '$bgdescript', '$bgdiff', '$bgplayer', '$bgrule', '$bgpublic', '$bgprice', '$imgContent', '$ref_bgtypeid', '$ref_bgclassid')";

        if ($conn->query($sql) === TRUE) {
            // ถ้าสำเร็จ เปลี่ยนเส้นทางไปยังหน้าที่แจ้งเตือนสำเร็จ
            echo "<script>alert('Boardgame added successfully!'); window.location.href='systemmanage.php';</script>";
        } else {
            // ถ้าไม่สำเร็จ แสดงข้อผิดพลาด
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
