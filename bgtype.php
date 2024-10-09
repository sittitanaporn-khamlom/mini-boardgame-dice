<link rel="stylesheet" href="css/bgtypes.css">
<?php
require 'DBcon.php'; // เชื่อมต่อฐานข้อมูล

// ฟังก์ชันสำหรับเพิ่มข้อมูล
if (isset($_POST['add_type'])) {
    $bgtype = $_POST['bgtype'];

    // ดึง ID ล่าสุด
    $sql = "SELECT MAX(bgtypeid) AS max_id FROM bgtype";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $new_id = $row['max_id'] + 1; // เพิ่ม 1 จาก ID ล่าสุด

    // ทำการเพิ่มข้อมูลลงในฐานข้อมูล
    $sql = "INSERT INTO bgtype (bgtypeid, bgtype) VALUES ('$new_id', '$bgtype')";
    if ($conn->query($sql) === TRUE) {
        // รีไดเรคกลับไปที่ bgtype.php
        header("Location: bgtype.php?success=add");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ฟังก์ชันสำหรับอัปเดตข้อมูล
if (isset($_POST['update_type'])) {
    $bgtypeid = $_POST['bgtypeid'];
    $bgtype = $_POST['bgtype'];

    $sql = "UPDATE bgtype SET bgtype='$bgtype' WHERE bgtypeid='$bgtypeid'";
    if ($conn->query($sql) === TRUE) {
        // รีไดเรคกลับไปที่ bgtype.php
        header("Location: bgtype.php?success=update");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ฟังก์ชันสำหรับลบข้อมูล
if (isset($_GET['delete_type'])) {
    $bgtypeid = $_GET['delete_type'];

    $sql = "DELETE FROM bgtype WHERE bgtypeid='$bgtypeid'";
    if ($conn->query($sql) === TRUE) {
        // รีไดเรคกลับไปที่ bgtype.php
        header("Location: bgtype.php?success=delete");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ดึงข้อมูลทั้งหมดจาก bgtype
$sql = "SELECT * FROM bgtype";
$result = $conn->query($sql);
?>

<div class="main-content">
    <h2>Manage Boardgame Types</h2>
    <form method="POST">
        <input type="text" name="bgtype" placeholder="Type Name" required>
        <input class='add'type="submit" name="add_type" value="Add Type">
    </form>

    <h3>Current Types</h3>
    <table border="1">
        <tr>
            <th>Type ID</th>
            <th>Type</th>
            <th>Edit</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['bgtypeid']}</td>
                        <td>{$row['bgtype']}</td>
                        <td>
                            <form method='POST' style='display:inline;'>
                                <input class='add-button' type='hidden' name='bgtypeid' value='{$row['bgtypeid']}'>
                                <input type='text' name='bgtype' value='{$row['bgtype']}' required>
                                <input class='update' type='submit' name='update_type'  value='Update'>
                            </form>
                            <a href='javascript:void(0);' onclick='confirmDelete({$row['bgtypeid']});' class='delete-button'>Delete</a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No types found.</td></tr>";
        }
        ?>
    </table>
    <a href="systemmanage.php" class="back-button">Back</a>
    <script>
function confirmDelete(bgtypeid) {
    if (confirm("Are you sure you want to delete this type?")) {
        window.location.href = "bgtype.php?delete_type=" + bgtypeid; // ส่งคำขอลบข้อมูล
    }
}
</script>
    
</div>


