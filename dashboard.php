<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])=="") {
    header("Location: index.php");
} else {
    // Restrict access to only admins and teachers
if($_SESSION['role'] !== "admin" && $_SESSION['role'] !== "teacher") {
    echo "<script>alert('Access Denied!'); window.location.href='index.php';</script>";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SRMS System | Dashboard</title>
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
                            <h2 class="title">Dashboard</h2>
                            <?php
if ($_SESSION['role'] === "teacher") {
    $teacherId = $_SESSION['tlogin']; // Use correct session variable for teacher
    $sql = "SELECT * FROM teacher_alerts 
            WHERE (teacherId IS NULL OR teacherId = :tid)
            AND deadline >= CURDATE()
            ORDER BY createdAt DESC
            LIMIT 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':tid', $teacherId, PDO::PARAM_STR);
    $query->execute();
    $alert = $query->fetch(PDO::FETCH_OBJ);

    if ($alert) {
        echo '<div class="alert alert-warning mt-3">
            <strong>📢 Alert:</strong> ' . htmlentities($alert->alertMessage) . '<br>
            <strong>Deadline:</strong> ' . htmlentities($alert->deadline) . '
        </div>';
    }
}
?>

                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="container-fluid">
                        <div class="row">
                            
                            <!-- Registered Users -->
                            <div class="col-lg-3 col-md-3 col-sm-6 mb-3">
                                <a class="dashboard-stat bg-primary" href="manage-students.php">
                                    <?php
                                    $sql1 ="SELECT COUNT(*) AS total FROM tblstudents";
                                    $query1 = $dbh->prepare($sql1);
                                    $query1->execute();
                                    $result1 = $query1->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <span class="number"><?php echo htmlentities($result1['total']); ?></span>
                                    <span class="name">Regd Users</span>
                                    <span class="bg-icon"><i class="fa fa-users"></i></span>
                                </a>
                            </div>

                            <!-- Subjects Listed -->
                            <div class="col-lg-3 col-md-3 col-sm-6 mb-3">
                                <a class="dashboard-stat bg-danger" href="manage-subjects.php">
                                    <?php
                                    $sql2 ="SELECT COUNT(*) AS total FROM tblsubjects";
                                    $query2 = $dbh->prepare($sql2);
                                    $query2->execute();
                                    $result2 = $query2->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <span class="number"><?php echo htmlentities($result2['total']); ?></span>
                                    <span class="name">Subjects Listed</span>
                                    <span class="bg-icon"><i class="fa fa-ticket"></i></span>
                                </a>
                            </div>

                            <!-- Teachers Listed -->
                            <div class="col-lg-3 col-md-3 col-sm-6 mb-3">
                                <a class="dashboard-stat bg-info" href="manage-teachers.php">

                                    <?php
                                    $sql3 ="SELECT COUNT(*) AS total FROM tblteachers";
                                    $query3 = $dbh->prepare($sql3);
                                    $query3->execute();
                                    $result3 = $query3->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <span class="number"><?php echo htmlentities($result3['total']); ?></span>
                                    <span class="name">Teachers Listed</span>
                                    <span class="bg-icon"><i class="fa fa-user"></i></span>
                                </a>
                            </div>

                            <!-- Total Classes -->
                            <div class="col-lg-3 col-md-3 col-sm-6 mb-3">
                                <a class="dashboard-stat bg-warning" href="manage-classes.php">

                                    <?php
                                    $sql4 ="SELECT COUNT(*) AS total FROM tblclasses";
                                    $query4 = $dbh->prepare($sql4);
                                    $query4->execute();
                                    $result4 = $query4->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <span class="number"><?php echo htmlentities($result4['total']); ?></span>
                                    <span class="name">Total Classes Listed</span>
                                    <span class="bg-icon"><i class="fa fa-bank"></i></span>
                                </a>
                            </div>

                            <!-- Results Declared -->
                            <div class="col-lg-3 col-md-3 col-sm-6 mb-3">
                                <a class="dashboard-stat bg-success" href="manage-results.php">

                                    <?php
                                    $sql5 ="SELECT COUNT(DISTINCT StudentId) AS total FROM tblresult";
                                    $query5 = $dbh->prepare($sql5);
                                    $query5->execute();
                                    $result5 = $query5->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <span class="number"><?php echo htmlentities($result5['total']); ?></span>
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

<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>

<?php } ?>
788888//