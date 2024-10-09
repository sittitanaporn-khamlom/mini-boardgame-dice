<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Makaan - Real Estate HTML Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@700;800&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="css/search.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

<?php
    require 'DBcon.php';  // Load once at the top
    session_start(); // Start session

    // Check if user is logged in
    if (!isset($_SESSION['memid'])) {
        header("Location: index.php"); // Redirect to login page if not logged in
        exit();
    }

    // Safe retrieval of memid
    $memid = intval($_SESSION['memid']);

    // Check if bgid is set and retrieve related data
    if (isset($_GET['bgid'])) {
        $bgid = intval($_GET['bgid']); // Safely retrieve bgid

        $sql = "SELECT bgm.*, bgc.bgclass, bgc.bgclassid, bgt.bgtype, bgc.bgclassimg
            FROM bgmanage bgm
            LEFT JOIN bgclass bgc ON bgm.ref_bgclassid = bgc.bgclassid 
            LEFT JOIN bgtype bgt ON bgm.ref_bgtypeid = bgt.bgtypeid
            WHERE bgm.bgid = ?";
    
    $stmt = $conn->prepare($sql);  // ใช้ prepared statement ป้องกัน SQL injection
    $stmt->bind_param('i', $bgid);  // กำหนดค่า bgid
    $stmt->execute();
    $resultget = $stmt->get_result();
        
        if (!$resultget) {
            die("Error fetching bgid: " . $conn->error);
        }
    }

    // Search Box Processing
    if (isset($_POST['keyword'])) {
        $keyword = $conn->real_escape_string($_POST['keyword']); // Sanitize input

        $sql = "SELECT bgm.*, bgc.bgclass, bgt.bgtype 
                FROM bgmanage bgm
                JOIN bgclass bgc ON bgm.ref_bgclassid = bgc.bgclassid 
                JOIN bgtype bgt ON bgm.ref_bgtypeid = bgt.bgtypeid
                WHERE bgm.bgname LIKE ?";

        $stmt = $conn->prepare($sql);
        $searchKeyword = '%' . $keyword . '%';
        $stmt->bind_param('s', $searchKeyword);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            die("Error executing search: " . $conn->error);
        }
    }
?>


    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar Start -->
        <div class="container-fluid nav-bar navbar-expand-lg bg-transparent">
        <nav class="navbar navbar-expand-lg bg-white navbar-light py-0 px-4 rounded">
                <a href="profile.php" class="navbar-brand d-flex align-items-center text-center">
                    <div class="icon p-2 me-2">
                    <?php 
                            $memid = $_SESSION['memid'];

                            // ดึงข้อมูลผู้ใช้
                            $user_query = "SELECT * FROM bgmem WHERE memid='$memid'";
                            $user_result = $conn->query($user_query);
                            
                            if ($user_result->num_rows > 0) {
                                $user_data = $user_result->fetch_assoc();
                            } else {
                                echo "เกิดข้อผิดพลาดในการดึงข้อมูลผู้ใช้.";
                            }                  
                        ?>
                        <img class="img-fluid" src="data:image/png;base64,<?php echo htmlspecialchars($user_data['memimg'] ?: base64_encode(file_get_contents('img/all.png')));?>" style="width: 50px; height: 50px; border-radius: 50px;">
                        </div>
                    <h1 class="m-0 text-primary">BGDice</h1>
                </a>
                <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto">

                    <form id="searchf" action="category.php" method="POST">
                    <div id="search">
                                <svg viewBox="0 0 420 60" xmlns="http://www.w3.org/2000/svg">
                                    <rect class="bar"/>
                                    
                                    <g class="magnifier">
                                        <circle class="glass"/>
                                        <line class="handle" x1="32" y1="32" x2="44" y2="44"></line>
                                    </g>

                                    <g class="sparks">
                                        <circle class="spark"/>
                                        <circle class="spark"/>
                                        <circle class="spark"/>
                                    </g>

                                    <g class="burst pattern-one">
                                        <circle class="particle circle"/>
                                        <path class="particle triangle"/>
                                        <circle class="particle circle"/>
                                        <path class="particle plus"/>
                                        <rect class="particle rect"/>
                                        <path class="particle triangle"/>
                                    </g>
                                    <g class="burst pattern-two">
                                        <path class="particle plus"/>
                                        <circle class="particle circle"/>
                                        <path class="particle triangle"/>
                                        <rect class="particle rect"/>
                                        <circle class="particle circle"/>
                                        <path class="particle plus"/>
                                    </g>
                                    <g class="burst pattern-three">
                                        <circle class="particle circle"/>
                                        <rect class="particle rect"/>
                                        <path class="particle plus"/>
                                        <path class="particle triangle"/>
                                        <rect class="particle rect"/>
                                        <path class="particle plus"/>
                                    </g>
                                </svg>
                                <input type="search" name="keyword" aria-label="Search for inspiration" placeholder="Search..."/>
                            </div>

                            <div id="results">
                            </div>
                        </form>

                        <a href="profile.php" class="nav-item nav-link"><?php echo htmlspecialchars($user_data['memname'])?></a>
                        <a href="mainmenu.php" class="nav-item nav-link">Home</a>
                        <a href="category.php" class="nav-item nav-link">BoardGame Category</a>
                        <a href="logout.php" class="nav-item nav-link">Log out</a>
                </div>
            </nav>
        </div>
        <!-- Navbar End -->


        <!-- Header Start -->
        <?php
            if (mysqli_num_rows($resultget) > 0) {
                while ($row = $resultget->fetch_assoc()) {
                    // ดึงข้อมูลจากผลการนับจำนวน bgclass และ bgtype
                    
                    // แปลงภาพเป็น base64
                    $imgData = base64_encode(string: $row['bgimg']);
                    $src = 'data:image/jpeg;base64,' . $imgData;

                    $imgData1 = base64_encode(string: $row['bgimg1']);
                    $src1 = 'data:image/jpeg;base64,' . $imgData1;

                    $imgData2 = base64_encode(string: $row['bgimg2']);
                    $src2 = 'data:image/jpeg;base64,' . $imgData2;

                    $imgData3 = base64_encode(string: $row['bgimg3']);
                    $src3 = 'data:image/jpeg;base64,' . $imgData3;
            
                    // แปลงภาพ bgclass เป็น base64
                    $imgDatacl = base64_encode($row['bgclassimg']);
                    $srccl = 'data:image/jpeg;base64,' . $imgDatacl;
                    $randCategoryId = rand(1, 20);
                echo '
        <div class="container-fluid header bg-white p-0">
            <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
                <div class="col-md-6 p-5 mt-lg-5">
                    <h1 class="display-5 animated fadeIn mb-4">'.$row['bgname'].'</h1> 
                        <nav aria-label="breadcrumb animated fadeIn">
                        <ol class="breadcrumb text-uppercase">
                            <li class="breadcrumb-item"><a href="mainmenu.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="">'.$row['bgname'].'</a></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 animated fadeIn">
                    <img class="img-fluid" src="'.$src1.'" alt="" style="width:1000px; height:500px; object-fit:cover;">
                </div>
            </div>
        </div>
        <!-- Header End -->


        <!-- Search Start -->
        <div class="container-fluid rounded bg-primary mb-5 wow fadeIn" data-wow-delay="0.1s" style="padding: 35px;">
            <div class="container">
            </div>
        </div>
        <!-- Search End -->

        <!-- Property List Start -->
        

        <!-- Call to Action Start -->

                <div class="container-xxl bg-white py-5">
    <div class="container">
        <div class="row g-0 gx-5 align-items-start">
            <div class="col-lg-6">
                <div class="text-start mb-5 wow slideInLeft" data-wow-delay="0.1s">
                    <h1 class="mb-3">'.$row['bgname'].'</h1>
                    <p class="text-dark">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row['bgdescript'].'</p>

                                <div class="col-lg-6 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                                    <div class="text-start mb-5 pt-5">
                                        <h4 class="d-block">🪨  Difficult: <i class="text-primary me-3 text-lg-start"></i>'.$row['bgdiff'].'/5</h4>
                                        <br>
                                        <h4 class="d-block">🚹  Player: <i class="text-primary me-3 text-lg-start"></i>2-'.$row['bgplayer'].' คน</h4>
                                        <br>
                                        <h4 class="d-block">💲  Price: <i class="text-primary me-3 text-lg-start"></i><b class="text-primary">'.$row['bgprice'].'</b>฿</h4>
                                    </div>
                                </div>


                </div>
            </div>
            <div class="property-item position-relative rounded col-lg-6">
                <img class="img-fluid rounded w-100" src="'.$src.'" alt="BoardGame Image" style="width: 400; height: auto; object-fit: cover;">
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                <a class="cat-item d-block bg-light text-center rounded p-3" href="category.php?idc='.$row['bgclassid'].'#category-'.$row['bgclassid'].'">
                    <div class="rounded p-4" id="category-'.$row['bgclassid'].'">
                        <div class="icon mb-3">
                            <img class="img-fluid" src="'.$srccl.'" alt="Icon" style="width: 45px; height: 45px;">
                        </div>
                        <h6>ID : '.$row['bgclassid'].'</h6>
                        <span>'.$row['bgclass'].'</span>
                    </div>
                </a>
            </div>
            
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                <a class="cat-item d-block bg-light text-center rounded p-3" href="category.php?idt='.$row['ref_bgtypeid'].'#category-'.$row['ref_bgtypeid'].'">
                    <div class="rounded p-4" id="category-'.$row['ref_bgtypeid'].'">
                        <div class="icon mb-3">
                            <img class="img-fluid" src="img/all.png" alt="Icon" style="width: 45px; height: 45px;">
                        </div>
                        <h6>ID : '.$row['ref_bgtypeid'].'</h6>
                        <span>'.$row['bgtype'].'</span>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                <a class="cat-item d-block bg-light text-center rounded p-3" href="category.php?idc='.$randCategoryId.'#category-'.$randCategoryId.'">
                    <div class="rounded p-4" id="category-'.$randCategoryId.'">
                        <div class="icon mb-3">
                            <img class="img-fluid" src="img/bgclassimg/bgrd.png" alt="Icon" style="width: 45px; height: 45px;">
                        </div>
                        <h6>ID : Random</h6>
                        <span>Random</span>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                <a class="cat-item d-block bg-light text-center rounded p-3" href="category.php">
                    <div class="rounded p-4" >
                        <div class="icon mb-3">
                            <img class="img-fluid" src="img/bgclassimg/bgc.png" alt="Icon" style="width: 45px; height: 45px;">
                        </div>
                        <h6>All BoardGame</h6>
                        <span>Category</span>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>

<div class="container-xxl bg-white py-5">
    <div class="container">
        <div class="bg-light rounded p-3">
            <div class="bg-white rounded p-4" style="border: 1px dashed rgba(0, 185, 142, .3)">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6 wow fadeIn d-flex align-items-start mt-5" data-wow-delay="0.1s">
                        <img class="img-fluid rounded w-100" src="'.$src3.'" alt="">
                    </div>
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                        <div class="mb-4">
                            <h1 class="mb-3">'.$row['bgname'].' Rule</h1>
                            <h2 class="mb-3">กฎและวิธีการเล่น</h2>
                        </div>
                    </div>
                    <p class="text-dark">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row['bgrule'].'</p>
                </div>
            </div>
        </div>
    </div>
</div>
';
            }
        } else {
            echo "No results";
        }            
            $conn->close();
            ?>      
        
        <!-- Call to Action End -->
        <!-- Property List End -->


        <!-- Footer Start -->
        <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5 wow fadeIn"
                            data-wow-delay="0.1s">
                            <div class="container py-5">
                                <div class="row g-5">
                                    <div class="col-lg-6 col-md-6 ">
                                        <h5 class="text-white mb-4">Contact us</h5>
                                        <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>RajaRajamangala University of Technology Thanyaburi.</p>
                                        <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+660810126012</p>
                                        <p class="mb-2"><i class="fa fa-envelope me-3"></i>ugkongthong@gmail.com</p>
                                        <div class="d-flex pt-2">
                                            <a class="btn btn-outline-light btn-social" href="https://x.com/SuzuMiya__YuuGi"><i
                                                    class="fab fa-twitter"></i></a>
                                            <a class="btn btn-outline-light btn-social" href="https://www.facebook.com/ug.kongthong"><i
                                                    class="fab fa-facebook-f"></i></a>
                                            <a class="btn btn-outline-light btn-social" href="https://www.youtube.com/@SuzuMiyaYuuGi"><i
                                                    class="fab fa-youtube"></i></a>
                                            <a class="btn btn-outline-light btn-social" href="https://github.com/SuzuMiyaYuuGi/webapp67"><i
                                                    class="fab fa-linkedin-in"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <h5 class="text-white mb-4">Quick Links</h5>
                                        <a class="btn btn-link text-white-50" href="mainmenu.php">Home</a>
                                        <a class="btn btn-link text-white-50" href="Category.php">Category</a>
                                        <a class="btn btn-link text-white-50" href="profile.php">Profile</a>
                                    </div>
                                </div>
                            </div>
                            <div class="container">
                                <div class="copyright">
                                    <div class="row">
                                        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                                            &copy; <a class="border-bottom" href="https://www.facebook.com/ug.kongthong">BGDice</a>, Mini Project Web-App.
                                            Designed By <a class="border-bottom" href="https://www.facebook.com/ug.kongthong">AGP</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Footer End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>