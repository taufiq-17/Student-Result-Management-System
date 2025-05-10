<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('includes/config.php');

// Debugging session issue
if (!isset($_SESSION['tlogin']) || empty($_SESSION['tlogin'])) {
    echo "<p style='color:red; text-align:center;'>Session not found! Redirecting to login page...</p>";
    header("refresh:3; url=index.php"); // Wait 3 seconds before redirecting
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SRMS System | Teacher Dashboard</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">
</head>

<body class="top-navbar-fixed">
<div class="main-wrapper">
    <?php include('includes/topbar.php'); ?>
    
    <div class="content-wrapper">
        <div class="content-container">
            <?php include('includes/leftbar.php'); ?>
            
            <div class="main-page">
                <div class="container-fluid">
                    <div class="row page-title-div">
                        <div class="col-sm-6">
                            <h2 class="title">Teacher Dashboard</h2>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- Registered Students -->
                            <div class="col-lg-3 col-md-3 col-sm-6">
                                <a class="dashboard-stat bg-primary" href="view-classes.php">
                                    <?php
                                    $sql1 = "SELECT COUNT(*) AS total FROM tblstudents";
                                    $query1 = $dbh->prepare($sql1);
                                    $query1->execute();
                                    $result1 = $query1->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <span class="number"><?php echo htmlentities($result1['total']); ?></span>
                                    <span class="name">Classes Assigned</span>
                                    <span class="bg-icon"><i class="fa fa-users"></i></span>
                                </a>
                            </div>

                            <!-- Subjects Listed -->
                            <div class="col-lg-3 col-md-3 col-sm-6">
                                <a class="dashboard-stat bg-danger" href="view-subjects.php">
                                    <?php
                                    $sql2 = "SELECT COUNT(*) AS total FROM tblsubjects";
                                    $query2 = $dbh->prepare($sql2);
                                    $query2->execute();
                                    $result2 = $query2->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <span class="number"><?php echo htmlentities($result2['total']); ?></span>
                                    <span class="name">Subjects Listed</span>
                                    <span class="bg-icon"><i class="fa fa-book"></i></span>
                                </a>
                            </div>

                            <!-- Classes Assigned -->
                            <div class="col-lg-3 col-md-3 col-sm-6">
                                <a class="dashboard-stat bg-info" href="view-students.php">
                                    <?php
                                    $sql3 = "SELECT COUNT(*) AS total FROM tblclasses";
                                    $query3 = $dbh->prepare($sql3);
                                    $query3->execute();
                                    $result3 = $query3->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <span class="number"><?php echo htmlentities($result3['total']); ?></span>
                                    <span class="name">Students Listed</span>
                                    <span class="bg-icon"><i class="fa fa-bank"></i></span>
                                </a>
                            </div>

                            <!-- Results Declared -->
                            <div class="col-lg-3 col-md-3 col-sm-6">
                                <a class="dashboard-stat bg-success" href="manage-results.php">
                                    <?php
                                    $sql4 = "SELECT COUNT(DISTINCT StudentId) AS total FROM tblresult";
                                    $query4 = $dbh->prepare($sql4);
                                    $query4->execute();
                                    $result4 = $query4->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <span class="number"><?php echo htmlentities($result4['total']); ?></span>
                                    <span class="name">Results Declared</span>
                                    <span class="bg-icon"><i class="fa fa-file-text"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Scripts (CDN version to fix dropdown) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<!--<script src="js/main.js"></script>-->

</body>
</html>
