<?php
require 'DBcon.php'; // เชื่อมต่อฐานข้อมูล

// ฟังก์ชันสำหรับอัปเดตข้อมูลสมาชิก
if (isset($_POST['update_mem'])) {
    $memid = $_POST['memid'];
    $memname = $_POST['memname'];
    $memlast = $_POST['memlast'];
    $mempass = $_POST['mempass'];
    $memage = $_POST['memage'];
    $memaddress = $_POST['memaddress'];
    $memimg = $_POST['meming'];

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพใหม่หรือไม่
    if (!empty($_FILES['memimg']['tmp_name'])) {
        $memimg = addslashes(file_get_contents($_FILES['memimg']['tmp_name'])); // อ่านข้อมูลไฟล์รูปภาพและแปลงเป็น string
        
        // อัปเดตข้อมูลรวมถึงรูปภาพ
        $sql = "UPDATE bgmem SET 
            memname='$memname', 
            memlast='$memlast', 
            mempass='$mempass', 
            memage='$memage', 
            memaddress='$memaddress', 
            memimg='$memimg'
            WHERE memid='$memid'";
    } else {
        // ตรวจสอบว่ามีรูปภาพในฐานข้อมูลแล้วหรือไม่
        $check_img_sql = "SELECT memimg FROM bgmem WHERE memid='$memid'";
        $result = $conn->query($check_img_sql);
        $row = $result->fetch_assoc();
        
        if (empty($row['memimg'])) {
            // ถ้าไม่มีรูปภาพในฐานข้อมูล ให้ใช้รูปภาพค่าเริ่มต้น
            $default_img_path = 'img/all.png';
            $memimg = addslashes(file_get_contents($default_img_path));
            
            // อัปเดตข้อมูลรวมถึงรูปภาพค่าเริ่มต้น
            $sql = "UPDATE bgmem SET 
                memname='$memname', 
                memlast='$memlast', 
                mempass='$mempass', 
                memage='$memage', 
                memaddress='$memaddress', 
                memimg='$memimg'
                WHERE memid='$memid'";
        } else {
            // อัปเดตข้อมูลโดยไม่รวมรูปภาพ
            $sql = "UPDATE bgmem SET 
                memname='$memname', 
                memlast='$memlast', 
                mempass='$mempass', 
                memage='$memage', 
                memaddress='$memaddress'
                WHERE memid='$memid'";
        }
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: memmanage.php?success=update");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ฟังก์ชันสำหรับลบสมาชิก
if (isset($_POST['delete_mem'])) {
    $memid = $_POST['memid'];

    // ลบข้อมูลจากฐานข้อมูล
    $sql = "DELETE FROM bgmem WHERE memid='$memid'";

    if ($conn->query($sql) === TRUE) {
        header("Location: memmanage.php?success=delete");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ดึงข้อมูลสมาชิกทั้งหมด
$sql = "SELECT * FROM bgmem";
$members = $conn->query($sql);

// ดึงจำนวนสมาชิกทั้งหมด
$sql_count = "SELECT COUNT(*) as total_members FROM bgmem";
$result_count = $conn->query($sql_count);
$total_members = $result_count->fetch_assoc()['total_members'];

// ดึงข้อมูลช่วงอายุสมาชิก
$sql_age = "SELECT memage, COUNT(*) as age_count FROM bgmem GROUP BY memage";
$result_age = $conn->query($sql_age);

$ages = [];
$age_counts = [];

while ($row = $result_age->fetch_assoc()) {
    $ages[] = $row['memage'];
    $age_counts[] = $row['age_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Management</title>
    <link rel="stylesheet" href="css/memmanage.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- ลิงก์เชื่อมต่อกับ Chart.js -->
</head>
<body>
    <div class="main-content">
        <h2>Member Management</h2>

        <!-- แสดงจำนวนสมาชิกทั้งหมด -->
        <div class="member-summary">
            <h3>Total Members: <?= $total_members ?></h3>
        </div>

        <!-- แสดงกราฟช่วงอายุสมาชิก -->
        <div class="chart-container" style="width: 50%; margin: 20px auto;">
            <canvas id="ageChart"></canvas>
        </div>

        <script>
        // ข้อมูลสำหรับกราฟช่วงอายุ
        const ageData = {
            labels: <?= json_encode($ages) ?>, // ช่วงอายุ
            datasets: [{
                label: 'Number of Members by Age',
                data: <?= json_encode($age_counts) ?>, // จำนวนสมาชิกแต่ละช่วงอายุ
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };

        // ตั้งค่ากราฟ
        const config = {
            type: 'bar',
            data: ageData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        // สร้างกราฟ
        const ageChart = new Chart(
            document.getElementById('ageChart'),
            config
        );
        </script>

        <!-- แสดงตารางสมาชิก -->
        <?php if ($members->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Password</th>
                    <th>Age</th>
                    <th>Address</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $members->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['memid'] ?></td>
                    <td><?= $row['memname'] ?></td>
                    <td><?= $row['memlast'] ?></td>
                    <td><?= $row['mempass'] ?></td>
                    <td><?= $row['memage'] ?></td>
                    <td><?= $row['memaddress'] ?></td>
                    <td>
                        <?php if (!empty($row['memimg'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($row['memimg']) ?>" width="50" height="50"/>
                        <?php else: ?>
                        <img src="img/all.png" width="50" height="50"/>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="memid" value="<?= $row['memid'] ?>">
                            <input type="submit" name="edit_mem" value="Edit">
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="memid" value="<?= $row['memid'] ?>">
                            <input type="submit" name="delete_mem" value="Delete" onclick="return confirm('Are you sure you want to delete this member?');">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No members found.</p>
        <?php endif; ?>

        <?php
        // ฟังก์ชันสำหรับการแก้ไขข้อมูลสมาชิก
        if (isset($_POST['edit_mem'])):
            $memid = $_POST['memid'];

            // ดึงข้อมูลของสมาชิกที่ต้องการแก้ไข
            $sql = "SELECT * FROM bgmem WHERE memid='$memid'";
            $result = $conn->query($sql);
            $member = $result->fetch_assoc();
        ?>
        <h3>Edit Member ID: <?= $member['memid'] ?></h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="memid" value="<?= $member['memid'] ?>">
            <div class="form-group">
                <label for="memname">First Name:</label>
                <input type="text" name="memname" value="<?= $member['memname'] ?>" required>
            </div>
            <div class="form-group">
                <label for="memlast">Last Name:</label>
                <input type="text" name="memlast" value="<?= $member['memlast'] ?>" required>
            </div>
            <div class="form-group">
                <label for="mempass">Password:</label>
                <input type="text" name="mempass" value="<?= $member['mempass'] ?>" required>
            </div>
            <div class="form-group">
                <label for="memage">Age:</label>
                <input type="number" name="memage" value="<?= $member['memage'] ?>" required>
            </div>
            <div class="form-group">
                <label for="memaddress">Address:</label>
                <input type="text" name="memaddress" value="<?= $member['memaddress'] ?>" required>
            </div>
            <div class="form-group">
                <label for="memimg">Image:</label>
                <input type="file" name="memimg" accept="image/*">
                <p>Current Image: <img src="data:image/jpeg;base64,<?= base64_encode($member['memimg']) ?>" width="50" height="50"/></p>
            </div>
            <button type="submit" name="update_mem">Update</button>
        </form>
        <?php endif; ?>
        <a href="systemmanage.php" class="back-button">Back</a>
    </div>
</body>
</html>
