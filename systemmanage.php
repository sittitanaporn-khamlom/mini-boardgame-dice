<?php 
session_start(); // Start the session
if (!isset($_SESSION['stID'])) {
    header("Location: index.php"); // Redirect to index.php if not logged in
    exit();
}

// Connect to the database
require 'DBcon.php';

// Fetch total number of members
$sql_count = "SELECT COUNT(*) as total_members FROM bgmem";
$result_count = $conn->query($sql_count);
$total_members = $result_count->fetch_assoc()['total_members'];

// Fetch age group data
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
    <title>System Management</title>
    <link rel="stylesheet" href="css/systemmanages.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="STAFF">
                <h1>STAFF</h1>
                <a href="systemmanage.php">HOME</a>
                <hr>
            </div>
            <ul class="menu">
                <a href="staffmanage.php" class="e-btn">STAFF Management</a>
                <hr>
                <a href="memmanage.php" class="e-btn">Member Management</a>
                <hr>
                <a href="gamemanage.php" class="btn">Boardgame Management</a>
                <hr>
                <a href="bgclass.php" class="btn">Boardgame Class</a>
                <hr>
                <a href="bgtype.php" class="btn">Boardgame Type</a>
                <hr>
                <a href="#" class="add-boardgame-btn" onclick="showInsert()">Add New Boardgame</a>
                <hr>
            </ul>
            <div class="logout">
                <a href="staffout.php">Log out</a>
            </div>
        </div>

        <div class="main-content">
            <div id="home" class="section">
                <h2>Boardgame Information</h2>
                
                <!-- Display total members -->
                <div class="member-summary">
                    <h3>Total Members: <?= $total_members ?></h3>
                </div>

                <!-- Display age chart -->
                <div class="chart-container" style="width: 50%; margin: 20px auto;">
                    <canvas id="ageChart"></canvas>
                </div>
                <script>
                    // Age data for chart
                    const ageData = {
                        labels: <?= json_encode($ages) ?>, // Age groups
                        datasets: [{
                            label: 'Number of Members by Age',
                            data: <?= json_encode($age_counts) ?>, // Member counts
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    };

                    // Chart configuration
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

                    // Create chart
                    const ageChart = new Chart(
                        document.getElementById('ageChart'),
                        config
                    );
                </script>

                <!-- Boardgame table -->
                <?php
                $sql = "SELECT bgmanage.*, bgtype.bgtype AS type_name, bgclass.bgclass AS class_name 
                        FROM bgmanage 
                        LEFT JOIN bgtype ON bgmanage.ref_bgtypeid = bgtype.bgtypeid 
                        LEFT JOIN bgclass ON bgmanage.ref_bgclassid = bgclass.bgclassid";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<table border='1'>
                            <tr>
                                <th>Boardgame ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Rule</th>
                                <th>Difficulty</th>
                                <th>Player</th>
                                <th>Release Date</th>
                                <th>Price</th>
                                <th>Image</th>
                                <th>Type</th>
                                <th>Category</th>
                            </tr>";
                    while ($row = $result->fetch_assoc()) {
                        $maxdes = (strlen($row['bgdescript']) > 200) ? substr($row['bgdescript'], 0, 200) . '...' : $row['bgdescript'];
                        $maxru = (strlen($row['bgrule']) > 200) ? substr($row['bgrule'], 0, 200) . '...' : $row['bgrule'];
                        echo "<tr>
                                <td>{$row['bgid']}</td>
                                <td>{$row['bgname']}</td>
                                <td>{$maxdes}</td>
                                <td>{$maxru}</td>
                                <td>{$row['bgdiff']}</td>
                                <td>{$row['bgplayer']}</td>
                                <td>{$row['bgpublic']}</td>
                                <td>{$row['bgprice']}</td>
                                <td><img src='data:image/jpeg;base64," . base64_encode($row['bgimg']) . "' width='50' height='50' /></td>
                                <td>{$row['type_name']}</td>
                                <td>{$row['class_name']}</td>
                            </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No boardgame data found.";
                }
                ?>

                <h2>Boardgame Types</h2>
                <?php
                $sql_type = "SELECT * FROM bgtype";
                $result_type = $conn->query($sql_type);

                if ($result_type->num_rows > 0) {
                    echo "<table border='1'>
                            <tr>
                                <th>Type ID</th>
                                <th>Type Name</th>
                            </tr>";
                    while ($row = $result_type->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['bgtypeid']}</td>
                                <td>{$row['bgtype']}</td>
                            </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No boardgame type data found.";
                }
                ?>

                <h2>Boardgame Categories</h2>
                <?php
                $sql_class = "SELECT * FROM bgclass";
                $result_class = $conn->query($sql_class);

                if ($result_class->num_rows > 0) {
                    echo "<table border='1'>
                            <tr>
                                <th>Category ID</th>
                                <th>Category Name</th>
                                <th>Category Image</th>
                            </tr>";
                    while ($row = $result_class->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['bgclassid']}</td>
                                <td>{$row['bgclass']}</td>
                                <td><img src='data:image/jpeg;base64," . base64_encode($row['bgclassimg']) . "' width='50' height='50' /></td>
                            </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No boardgame category data found.";
                }

                // Close the database connection
                $conn->close();
                ?>
            </div>

            <div id="insert" class="section" style="display: none;">
                <h2>Add New Boardgame</h2>
                <?php
                require 'DBcon.php';

                // Fetch Type options
                $typeQuery = "SELECT bgtypeid, bgtype FROM bgtype";
                $typeResult = mysqli_query($conn, $typeQuery);

                // Fetch Category options
                $classQuery = "SELECT bgclassid, bgclass FROM bgclass";
                $classResult = mysqli_query($conn, $classQuery);
                ?>

                <form action="addbgsuccess.php" method="post" enctype="multipart/form-data">
                    <div>
                        <input type="text" name="bgname" placeholder="Boardgame Name" required>
                    </div>
                    <div>
                        <textarea name="bgdescript" placeholder="Description" required></textarea>
                    </div>
                    <div>
                        <input type="number" name="bgplayer" placeholder="Number of Players" required>
                    </div>
                    <div>
                        <input type="text" name="bgrule" placeholder="Game Rules" required>
                    </div>
                    <div>
                        <input type="number" name="bgdiff" placeholder="Difficulty Level" required>
                    </div>
                    <div>
                        <input type="date" name="bgpublic" required>
                    </div>
                    <div>
                        <input type="number" name="bgprice" placeholder="Price" required>
                    </div>
                    <div>
                        <input type="file" name="bgimg" required>
                    </div>
                    <div>
                        <select name="ref_bgtypeid" required>
                            <option value="" disabled selected>Select Type</option>
                            <?php while ($row = mysqli_fetch_assoc($typeResult)) : ?>
                                <option value="<?= $row['bgtypeid'] ?>"><?= $row['bgtype'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <select name="ref_bgclassid" required>
                            <option value="" disabled selected>Select Category</option>
                            <?php while ($row = mysqli_fetch_assoc($classResult)) : ?>
                                <option value="<?= $row['bgclassid'] ?>"><?= $row['bgclass'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <input type="submit" value="Add Boardgame">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showInsert() {
            document.getElementById("insert").style.display = "block";
            document.getElementById("home").style.display = "none";
        }

        function showHome() {
            document.getElementById("insert").style.display = "none";
            document.getElementById("home").style.display = "block";
        }
    </script>
</body>
</html>
