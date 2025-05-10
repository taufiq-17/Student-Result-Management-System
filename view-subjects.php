<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('includes/config.php');

// Check teacher session
if (!isset($_SESSION['tlogin']) || empty($_SESSION['tlogin'])) {
    echo "<p style='color:red; text-align:center;'>Session not found! Redirecting to login page...</p>";
    header("refresh:3; url=index.php");
    exit();
}

// Get teacher userId from session
$teacherUserId = $_SESSION['tlogin'];

// Fetch assigned subjects
$sql = "SELECT s.id, s.SubjectName, s.SubjectCode, s.CreationDate
        FROM tblsubjects s
        INNER JOIN teacher_subject_map tsm ON s.id = tsm.subjectId
        WHERE tsm.teacherId = :teacherUserId";

$query = $dbh->prepare($sql);
$query->bindParam(':teacherUserId', $teacherUserId, PDO::PARAM_STR);
$query->execute();
$subjects = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Subjects | Teacher</title>
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
                        <div class="col-md-6">
                            <h2 class="title">Assigned Subjects</h2>
                        </div>
                    </div>

                    <section class="section">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Subjects List</h3>
                                        </div>
                                        <div class="panel-body">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Subject Name</th>
                                                        <th>Subject Code</th>
                                                        <th>Created On</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if ($subjects) {
                                                        $cnt = 1;
                                                        foreach ($subjects as $row) {
                                                            echo "<tr>";
                                                            echo "<td>" . htmlentities($cnt++) . "</td>";
                                                            echo "<td>" . htmlentities($row->SubjectName) . "</td>";
                                                            echo "<td>" . htmlentities($row->SubjectCode) . "</td>";
                                                            echo "<td>" . htmlentities($row->CreationDate) . "</td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='4' style='text-align:center;'>No subjects assigned.</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
