<?php
session_start();
error_reporting(0);
include('includes/config.php');

$msg = "";  // Initialize success message variable
$error = ""; // Initialize error message variable

if ((!isset($_SESSION['alogin']) || $_SESSION['alogin'] == "") && 
    (!isset($_SESSION['tlogin']) || $_SESSION['tlogin'] == "")) {
    header("Location: index.php");
    exit();
}

$stid = intval($_GET['stid']);

// Determine user role
$isAdmin = isset($_SESSION['alogin']) && $_SESSION['alogin'] != "";
$isTeacher = isset($_SESSION['tlogin']) && $_SESSION['tlogin'] != "";
$teacherSubjects = [];

if ($isTeacher) {
    $teacherId = $_SESSION['tlogin']; // assuming tlogin stores userId
    $stmt = $dbh->prepare("SELECT SubjectId FROM teacher_subject_map WHERE teacherId = :tid");
    $stmt->bindParam(':tid', $teacherId, PDO::PARAM_STR);
    $stmt->execute();
    $teacherSubjects = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

if (isset($_POST['submit'])) {
    $rowid = $_POST['id'];
    $marks = $_POST['marks']; 

    foreach ($_POST['id'] as $count => $id) {
        $mrks = $marks[$count];
        $iid = $rowid[$count];

        // Check if teacher is allowed to update this subject
        if ($isTeacher) {
            $stmt = $dbh->prepare("SELECT SubjectId FROM tblresult WHERE id = :iid LIMIT 1");
            $stmt->bindParam(':iid', $iid, PDO::PARAM_STR);
            $stmt->execute();
            $subjId = $stmt->fetchColumn();

            if (!in_array($subjId, $teacherSubjects)) {
                continue; // skip this update
            }
        }

        $sql = "UPDATE tblresult SET marks = :mrks WHERE id = :iid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':mrks', $mrks, PDO::PARAM_STR);
        $query->bindParam(':iid', $iid, PDO::PARAM_STR);
        $query->execute();

        $msg = "Result info updated successfully";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SMS Admin| Student result info</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/animate-css/animate.min.css">
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css">
    <link rel="stylesheet" href="css/prism/prism.css">
    <link rel="stylesheet" href="css/select2/select2.min.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="js/modernizr/modernizr.min.js"></script>
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
                            <h2 class="title">Student Result Info</h2>
                        </div>
                    </div>
                    <div class="row breadcrumb-div">
                        <div class="col-md-6">
                            <ul class="breadcrumb">
                                <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                <li class="active">Result Info</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h5>Update the Result info</h5>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?php if ($msg) { ?>
                                        <div class="alert alert-success left-icon-alert" role="alert">
                                            <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
                                        </div>
                                    <?php } else if ($error) { ?>
                                        <div class="alert alert-danger left-icon-alert" role="alert">
                                            <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                        </div>
                                    <?php } ?>

                                    <form class="form-horizontal" method="post">
                                        <?php
                                        if ($isTeacher) {
                                            $ret = "SELECT tblstudents.StudentName, tblclasses.ClassName, tblclasses.Section
                                                    FROM tblresult
                                                    JOIN tblstudents ON tblresult.StudentId = tblstudents.StudentId
                                                    JOIN tblclasses ON tblclasses.id = tblstudents.ClassId
                                                    JOIN teacher_class_map ON teacher_class_map.classId = tblclasses.id
                                                    WHERE tblstudents.StudentId = :stid AND teacher_class_map.teacherId = :tid
                                                    LIMIT 1";
                                            $stmt = $dbh->prepare($ret);
                                            $stmt->bindParam(':stid', $stid, PDO::PARAM_STR);
                                            $stmt->bindParam(':tid', $teacherId, PDO::PARAM_STR);
                                        } else {
                                            $ret = "SELECT tblstudents.StudentName, tblclasses.ClassName, tblclasses.Section
                                                    FROM tblresult
                                                    JOIN tblstudents ON tblresult.StudentId = tblstudents.StudentId
                                                    JOIN tblclasses ON tblclasses.id = tblstudents.ClassId
                                                    WHERE tblstudents.StudentId = :stid
                                                    LIMIT 1";
                                            $stmt = $dbh->prepare($ret);
                                            $stmt->bindParam(':stid', $stid, PDO::PARAM_STR);
                                        }

                                        $stmt->execute();
                                        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
                                        if ($stmt->rowCount() > 0) {
                                            foreach ($result as $row) {
                                        ?>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Class</label>
                                                <div class="col-sm-10">
                                                    <?php echo htmlentities($row->ClassName) . " (" . htmlentities($row->Section) . ")"; ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Full Name</label>
                                                <div class="col-sm-10">
                                                    <?php echo htmlentities($row->StudentName); ?>
                                                </div>
                                            </div>
                                        <?php } }

                                        $sql = "SELECT tblsubjects.id as subjectid, tblsubjects.SubjectName, tblresult.marks, tblresult.id as resultid
                                                FROM tblresult
                                                JOIN tblstudents ON tblstudents.StudentId = tblresult.StudentId
                                                JOIN tblsubjects ON tblsubjects.id = tblresult.SubjectId
                                                JOIN tblclasses ON tblclasses.id = tblstudents.ClassId
                                                WHERE tblstudents.StudentId = :stid";
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':stid', $stid, PDO::PARAM_STR);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) {
                                                if ($isAdmin || in_array($result->subjectid, $teacherSubjects)) {
                                        ?>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label"><?php echo htmlentities($result->SubjectName); ?></label>
                                                <div class="col-sm-10">
                                                    <input type="hidden" name="id[]" value="<?php echo htmlentities($result->resultid); ?>">
                                                    <input type="text" name="marks[]" class="form-control" value="<?php echo htmlentities($result->marks); ?>" maxlength="5" required>
                                                </div>
                                            </div>
                                        <?php } } } ?>

                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button type="submit" name="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/pace/pace.min.js"></script>
<script src="js/lobipanel/lobipanel.min.js"></script>
<script src="js/iscroll/iscroll.js"></script>
<script src="js/prism/prism.js"></script>
<script src="js/select2/select2.min.js"></script>
<script src="js/main.js"></script>
<script>
    $(function () {
        $(".js-states").select2();
        $(".js-states-limit").select2({ maximumSelectionLength: 2 });
        $(".js-states-hide").select2({ minimumResultsForSearch: Infinity });
    });
</script>
</body>
</html>