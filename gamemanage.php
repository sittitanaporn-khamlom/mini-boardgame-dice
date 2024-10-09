<link rel="stylesheet" href="css/gamemanage.css">
<script>
function showGameDetails() {
    const form = document.getElementById('gameForm');
    const bgid = form.bgid.value;

    // ส่งค่า bgid ไปยัง server โดยไม่ต้องกดปุ่ม
    form.submit();
}

// ฟังก์ชันเพื่อแสดง alert เมื่อมีการอัปเดตหรือลบ
function showAlert(message, redirectUrl) {
    alert(message);
    // เปลี่ยนหน้าไปยัง redirectUrl หลังจากแสดง alert
    setTimeout(() => {
        window.location.href = redirectUrl;
    }, 1000); // หน้าจะเปลี่ยนหลังจาก 1 วินาที
}

// แสดง alert ถ้ามีการอัปเดตหรือลบ
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        const successType = urlParams.get('success');
        if (successType === 'update') {
            showAlert('Updated successfully!', 'gamemanage.php');
        } else if (successType === 'delete') {
            showAlert('Deleted successfully!', 'gamemanage.php');
        }
    }
};
</script>

<?php
require 'DBcon.php'; // เชื่อมต่อฐานข้อมูล

// ฟังก์ชันสำหรับอัปเดตข้อมูล
if (isset($_POST['update_game'])) {
    $bgid = $_POST['bgid'];
    $bgname = $_POST['bgname'];
    $bgdescript = $_POST['bgdescript'];
    $bgdiff = $_POST['bgdiff'];
    $bgplayer = $_POST['bgplayer'];
    $bgrule = $_POST['bgrule'];
    $bgpublic = $_POST['bgpublic'];
    $bgprice = $_POST['bgprice'];
    $bgtypeid = $_POST['bgtypeid'];
    $bgclassid = $_POST['bgclassid'];

    // เช็คการอัปโหลดไฟล์ภาพ
    $bgimg = null;
    $bgimg1 = null;
    $bgimg2 = null;
    $bgimg3 = null;

    if (isset($_FILES['bgimg']) && $_FILES['bgimg']['error'] == 0) {
        $bgimg = addslashes(file_get_contents($_FILES['bgimg']['tmp_name']));
    }
    if (isset($_FILES['bgimg1']) && $_FILES['bgimg1']['error'] == 0) {
        $bgimg1 = addslashes(file_get_contents($_FILES['bgimg1']['tmp_name']));
    }
    if (isset($_FILES['bgimg2']) && $_FILES['bgimg2']['error'] == 0) {
        $bgimg2 = addslashes(file_get_contents($_FILES['bgimg2']['tmp_name']));
    }
    if (isset($_FILES['bgimg3']) && $_FILES['bgimg3']['error'] == 0) {
        $bgimg3 = addslashes(file_get_contents($_FILES['bgimg3']['tmp_name']));
    }

    // อัปเดตข้อมูลลงในฐานข้อมูล
    $sql = "UPDATE bgmanage SET 
        bgname='$bgname', 
        bgdescript='$bgdescript', 
        bgdiff='$bgdiff', 
        bgplayer='$bgplayer', 
        bgrule='$bgrule', 
        bgpublic='$bgpublic', 
        bgprice='$bgprice', 
        ref_bgtypeid='$bgtypeid', 
        ref_bgclassid='$bgclassid'";
    
    // ถ้ามีการอัปโหลดรูปภาพให้เพิ่มเข้าไปในคำสั่ง SQL
    if ($bgimg) {
        $sql .= ", bgimg='$bgimg'";
    }
    if ($bgimg1) {
        $sql .= ", bgimg1='$bgimg1'";
    }
    if ($bgimg2) {
        $sql .= ", bgimg2='$bgimg2'";
    }
    if ($bgimg3) {
        $sql .= ", bgimg3='$bgimg3'";
    }
    
    $sql .= " WHERE bgid='$bgid'";
    
    if ($conn->query($sql) === TRUE) {
        // รีไดเรคกลับไปที่ gamemanage.php
        header("Location: gamemanage.php?success=update");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ฟังก์ชันสำหรับลบข้อมูล
if (isset($_POST['delete_game'])) {
    $bgid = $_POST['bgid'];

    // ลบข้อมูลจากฐานข้อมูล
    $sql = "DELETE FROM bgmanage WHERE bgid='$bgid'";
    
    if ($conn->query($sql) === TRUE) {
        // รีไดเรคกลับไปที่ gamemanage.php
        header("Location: gamemanage.php?success=delete");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ฟังก์ชันสำหรับดึงข้อมูลเมื่อเลือก bgid
$selected_game = null;
if (isset($_POST['bgid'])) {
    $bgid = $_POST['bgid'];

    // ดึงข้อมูลจากฐานข้อมูล
    $sql = "SELECT * FROM bgmanage WHERE bgid='$bgid'";
    $result = $conn->query($sql);
    $selected_game = $result->fetch_assoc();
}

// ดึงข้อมูลประเภทและหมวดหมู่
$sql_type = "SELECT bgtypeid, bgtype FROM bgtype"; // แทนที่ bgtype ด้วยชื่อจริงของตารางประเภท
$sql_class = "SELECT bgclassid, bgclass FROM bgclass"; // แทนที่ bgclass ด้วยชื่อจริงของตารางหมวดหมู่
$types = $conn->query($sql_type);
$classes = $conn->query($sql_class);

// ดึงข้อมูลบอร์ดเกมทั้งหมดสำหรับแสดงในตาราง
$sql_all_games = "SELECT 
                        bgmanage.bgid, 
                        bgmanage.bgname, 
                        bgmanage.bgprice, 
                        bgtype.bgtype, 
                        bgclass.bgclass 
                    FROM 
                        bgmanage 
                    LEFT JOIN 
                        bgtype ON bgmanage.ref_bgtypeid = bgtype.bgtypeid 
                    LEFT JOIN 
                        bgclass ON bgmanage.ref_bgclassid = bgclass.bgclassid";
$all_games = $conn->query($sql_all_games);
?>

<div class="main-content">
    <h2>Boardgame Management</h2>
    
    <form id="gameForm" method="POST">
        <label for="bgid">Select Boardgame ID:</label>
        <select name="bgid" onchange="showGameDetails()" required>
            <option value="">---Select Edit Boardgame---</option>
            <?php
            // ดึงข้อมูลบอร์ดเกมทั้งหมดสำหรับแสดงใน dropdown
            $games_sql = "SELECT bgid, bgname FROM bgmanage";
            $games = $conn->query($games_sql);
            if ($games->num_rows > 0) {
                while ($row = $games->fetch_assoc()) {
                    echo "<option value='{$row['bgid']}'>{$row['bgid']} - {$row['bgname']}</option>";
                }
            }
            ?>
        </select>
    </form>

    <?php if ($selected_game): ?>
        <h3>Boardgame ID: <?= $selected_game['bgid'] ?></h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="bgid" value="<?= $selected_game['bgid'] ?>">
            
            <div class="form-group">
                <label for="bgname">Name:</label>
                <input type="text" name="bgname" value="<?= $selected_game['bgname'] ?>" required>
            </div>

            <div class="form-group">
                <label for="bgdescript">Description:</label>
                <input type="text" name="bgdescript" value="<?= $selected_game['bgdescript'] ?>" required>
            </div>

            <div class="form-group">
                <label for="bgdiff">Difficulty:</label>
                <input type="number" name="bgdiff" value="<?= $selected_game['bgdiff'] ?>" required>
            </div>

            <div class="form-group">
                <label for="bgplayer">Players:</label>
                <input type="number" name="bgplayer" value="<?= $selected_game['bgplayer'] ?>" required>
            </div>

            <div class="form-group">
                <label for="bgrule">Rules:</label>
                <input type="text" name="bgrule" value="<?= $selected_game['bgrule'] ?>" required>
            </div>

            <div class="form-group">
                <label for="bgpublic">Release Date:</label>
                <input type="date" name="bgpublic" value="<?= $selected_game['bgpublic'] ?>" required>
            </div>

            <div class="form-group">
                <label for="bgprice">Price:</label>
                <input type="number" name="bgprice" value="<?= $selected_game['bgprice'] ?>" required>
            </div>

            <!-- ประเภท -->
            <div class="form-group">
                <label for="bgtypeid">Boardgame Type:</label>
                <select name="bgtypeid" required>
                    <?php
                    if ($types->num_rows > 0) {
                        while ($row = $types->fetch_assoc()) {
                            $selected = ($row['bgtypeid'] == $selected_game['ref_bgtypeid']) ? 'selected' : '';
                            echo "<option value='{$row['bgtypeid']}' $selected>{$row['bgtype']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <!-- หมวดหมู่ -->
            <div class="form-group">
                <label for="bgclassid">Boardgame Class:</label>
                <select name="bgclassid" required>
                    <?php
                    if ($classes->num_rows > 0) {
                        while ($row = $classes->fetch_assoc()) {
                            $selected = ($row['bgclassid'] == $selected_game['ref_bgclassid']) ? 'selected' : '';
                            echo "<option value='{$row['bgclassid']}' $selected>{$row['bgclass']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <!-- อัปโหลดรูปภาพ -->
            <div class="form-group">
                <label for="bgimg">Image:</label>
                <input type="file" name="bgimg">
            </div>

            <div class="form-group">
                <label for="bgimg1">Image 1:</label>
                <input type="file" name="bgimg1">
            </div>

            <div class="form-group">
                <label for="bgimg2">Image 2:</label>
                <input type="file" name="bgimg2">
            </div>

            <div class="form-group">
                <label for="bgimg3">Image 3:</label>
                <input type="file" name="bgimg3">
            </div>

            <div class="form-actions">
                <button type="submit" name="update_game">Update</button>
                <button type="submit" name="delete_game" onclick="return confirm('Are you sure you want to delete this game?')">Delete</button>
            </div>
        </form>
    <?php endif; ?>

    <h3>All Boardgames</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Type</th>
                <th>Class</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($all_games->num_rows > 0) {
                while ($row = $all_games->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['bgid']}</td>
                        <td>{$row['bgname']}</td>
                        <td>{$row['bgprice']}</td>
                        <td>{$row['bgtype']}</td>
                        <td>{$row['bgclass']}</td>
                    </tr>";
                }
            }
            ?>
        </tbody>
    </table>
    <a href="systemmanage.php" class="back-button">Back</a>
</div>

<?php $conn->close(); ?>
