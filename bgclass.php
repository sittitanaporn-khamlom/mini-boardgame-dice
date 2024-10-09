<link rel="stylesheet" href="css/bgclass.css">
<?php
require 'DBcon.php'; // เชื่อมต่อฐานข้อมูล

// ฟังก์ชันสำหรับเพิ่มข้อมูล
if (isset($_POST['add_class'])) {
    $bgclass = $_POST['bgclass'];
    
    // ตรวจสอบว่ามีการอัปโหลดไฟล์
    if (isset($_FILES['bgclassimg']) && $_FILES['bgclassimg']['error'] === UPLOAD_ERR_OK) {
        $bgclassimg = addslashes(file_get_contents($_FILES['bgclassimg']['tmp_name'])); // เข้ารหัสไฟล์ภาพ
    } else {
        $bgclassimg = NULL; // ถ้าไม่มีการอัปโหลดภาพให้ตั้งค่าเป็น NULL
    }

    // ดึง ID ล่าสุด
    $sql = "SELECT MAX(bgclassid) AS max_id FROM bgclass";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $new_id = $row['max_id'] + 1; // เพิ่ม 1 จาก ID ล่าสุด

    // ทำการเพิ่มข้อมูลลงในฐานข้อมูล
    $sql = "INSERT INTO bgclass (bgclassid, bgclass, bgclassimg) VALUES ('$new_id', '$bgclass', '$bgclassimg')";
    if ($conn->query($sql) === TRUE) {
        header("Location: bgclass.php?success=add");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ฟังก์ชันสำหรับอัปเดตข้อมูล
if (isset($_POST['update_class'])) {
    $bgclassid = $_POST['bgclassid'];
    $bgclass = $_POST['bgclass'];

    $sql = "UPDATE bgclass SET bgclass='$bgclass' WHERE bgclassid='$bgclassid'";
    if ($conn->query($sql) === TRUE) {
        header("Location: bgclass.php?success=update");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ฟังก์ชันสำหรับลบข้อมูล
if (isset($_GET['delete_class'])) {
    $bgclassid = $_GET['delete_class'];

    $sql = "DELETE FROM bgclass WHERE bgclassid='$bgclassid'";
    if ($conn->query($sql) === TRUE) {
        header("Location: bgclass.php?success=delete");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ดึงข้อมูลทั้งหมดจาก bgclass
$sql = "SELECT * FROM bgclass";
$result = $conn->query($sql);
?>

<div class="main-content">
    <h2>Boardgame Class Management</h2>
    <form method="POST" enctype="multipart/form-data"> <!-- เพิ่ม enctype -->
        <input type="text" name="bgclass" placeholder="Class Name" required>
        <input type="file" name="bgclassimg" required> <!-- ฟิลด์อัปโหลดไฟล์ -->
        <input type="submit" name="add_class" value="Add Class">
    </form>
    
    <h3>Current Classes</h3>
    <table border="1">
        <tr>
            <th>Class ID</th>
            <th>Class</th>
            <th>Image</th> <!-- เพิ่มคอลัมน์สำหรับแสดงภาพ -->
            <th>Edit</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['bgclassid']}</td>
                        <td>{$row['bgclass']}</td>
                        <td><img src='data:image/jpeg;base64," . base64_encode($row['bgclassimg']) . "' width='50' height='50' /></td> <!-- แสดงภาพ -->
                        <td>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='bgclassid' value='{$row['bgclassid']}'>
                                <input type='text' name='bgclass' value='{$row['bgclass']}' required>
                                <input class='update-btn' type='submit' name='update_class' value='Update'> <!-- ใช้ class update-btn -->
                            </form>
                            <a class='delete-btn' href='javascript:void(0);' onclick='confirmDelete({$row['bgclassid']});'>Delete</a> <!-- ใช้ class delete-btn -->
                        </td>
                    </tr>";
            }
        } else {
            echo "No classes found.";
        }
        ?>
    </table>
    <a class="back-btn" href="systemmanage.php" >Back</a>

<script>
function confirmDelete(bgclassid) {
    if (confirm("Are you sure you want to delete this class?")) {
        window.location.href = "bgclass.php?delete_class=" + bgclassid; // เปลี่ยนไปยัง URL สำหรับลบข้อมูล
    }
}
</script>
    
</div>
