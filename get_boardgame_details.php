<?php
require 'DBcon.php';

// ฟังก์ชันเพื่อดึงรายละเอียดบอร์ดเกมตาม bgid
if (isset($_POST['bgid'])) {
    $bgid = $_POST['bgid'];
    $sql = "SELECT * FROM bgmanage WHERE bgid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $bgid);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response = [
            'bgid' => $row['bgid'],
            'bgname' => $row['bgname'],
            'bgdescript' => $row['bgdescript'],
            'bgplayer' => $row['bgplayer'],
            'bgrule' => $row['bgrule'],
            'bgdiff' => $row['bgdiff'],
            'bgpublic' => $row['bgpublic'],
            'bgprice' => $row['bgprice'],
            'bgimg' => base64_encode($row['bgimg']),
            'ref_bgtypeid' => $row['ref_bgtypeid'],  // เพิ่มประเภทบอร์ดเกม
            'ref_bgclassid' => $row['ref_bgclassid'],  // เพิ่มคลาสบอร์ดเกม
        ];
        echo json_encode($response);
    } else {
        echo json_encode([]);
    }
}

// ฟังก์ชันเพื่ออัปเดต bgrule
if (isset($_POST['update_bgrule'], $_POST['bgid'])) {
    $bgid = $_POST['bgid'];
    $new_bgrule = $_POST['update_bgrule'];

    $sql = "UPDATE bgmanage SET bgrule = ? WHERE bgid = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("si", $new_bgrule, $bgid);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => 'Updated successfully']);
        } else {
            echo json_encode(['error' => 'No rows affected or update failed']);
        }
    } else {
        echo json_encode(['error' => 'Failed to prepare SQL statement']);
    }
}
?>
}
?>
