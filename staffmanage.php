<?php
require 'DBcon.php'; // Connect to the database

// Function to add staff
if (isset($_POST['add_staff'])) {
    $stID = $_POST['stID'];
    $stPass = $_POST['stPass'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO bgstaff (stID, stPass) VALUES (?, ?)");
    $stmt->bind_param("ss", $stID, $stPass);

    if ($stmt->execute()) {
        header("Location: staffmanage.php?success=add");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Function to remove staff
if (isset($_POST['remove_staff'])) {
    $stID = $_POST['stID'];

    // Confirm before deletion
    $stmt = $conn->prepare("DELETE FROM bgstaff WHERE stID = ?");
    $stmt->bind_param("s", $stID);

    if ($stmt->execute()) {
        header("Location: staffmanage.php?success=remove");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Function to change password
if (isset($_POST['change_pass'])) {
    $stID = $_POST['stID'];
    $newPass = $_POST['newPass'];

    $stmt = $conn->prepare("UPDATE bgstaff SET stPass = ? WHERE stID = ?");
    $stmt->bind_param("ss", $newPass, $stID);

    if ($stmt->execute()) {
        header("Location: staffmanage.php?success=change");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Show alert message if there was a successful operation
function showAlert() {
    if (isset($_GET['success'])) {
        $successType = $_GET['success'];
        if ($successType === 'add') {
            echo "<script>alert('Staff added successfully!');</script>";
        } else if ($successType === 'remove') {
            echo "<script>alert('Staff removed successfully!');</script>";
        } else if ($successType === 'change') {
            echo "<script>alert('Password changed successfully!');</script>";
        }
    }
}

showAlert();

// Fetch all staff members
$staff_query = "SELECT * FROM bgstaff";
$staff_result = $conn->query($staff_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stm.css">
    <title>STAFF Management</title>
</head>
<body>
    <div class="main-content">
        <h2>STAFF Management</h2>

        <form method="POST">
            <h3>Add STAFF</h3>
            <input type="text" name="stID" placeholder="Staff ID" required>
            <input type="password" name="stPass" placeholder="Password" required>
            <input class="add-btn" type="submit" name="add_staff" value="Add STAFF">
        </form>

        <h3>STAFF List</h3>
        <table>
            <thead>
                <tr>
                    <th>STAFF ID</th>
                    <th>Password</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($staff_result->num_rows > 0): ?>
                    <?php while ($row = $staff_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['stID']); ?></td>
                            <td><?php echo htmlspecialchars($row['stPass']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="stID" value="<?php echo htmlspecialchars($row['stID']); ?>">
                                    <input type="password" name="newPass" placeholder="New Password" required>
                                    <input class="change" type="submit" name="change_pass" value="Change Password">
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="stID" value="<?php echo htmlspecialchars($row['stID']); ?>">
                                    <input type="submit" name="remove_staff" value="Delete STAFF" class="delete-button" onclick="return confirm('Are you sure you want to remove this staff member?');">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No staff members found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="systemmanage.php" class="back-button">Back</a>
    </div>
</body>
</html>
