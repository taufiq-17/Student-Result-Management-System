<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0 || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

if (isset($_POST['submit'])) {
    $alertMessage = $_POST['alertMessage'];
    $deadline = $_POST['deadline'];
    $teacherId = $_POST['teacherId'] !== 'all' ? $_POST['teacherId'] : null;

    $sql = "INSERT INTO teacher_alerts (alertMessage, deadline, teacherId) VALUES (:alertMessage, :deadline, :teacherId)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':alertMessage', $alertMessage, PDO::PARAM_STR);
    $query->bindParam(':deadline', $deadline, PDO::PARAM_STR);
    $query->bindParam(':teacherId', $teacherId, PDO::PARAM_STR);
    $query->execute();

    // Redirect to the same page with a success flag
    header("Location: create-alert.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Teacher Alert</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">
</head>
<body class="top-navbar-fixed">
<?php include('includes/topbar.php'); ?>
<div class="main-wrapper">
    <div class="content-wrapper">
        <div class="content-container">
            <?php include('includes/leftbar.php'); ?>
            <div class="main-page">
                <div class="container-fluid">
                    <div class="row page-title-div">
                        <div class="col-sm-6">
                            <h2 class="title">Send Alert to Teachers</h2>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">

                                <?php if(isset($_GET['success'])){ ?>
                                    <div class="alert alert-success text-center">Alert sent successfully!</div>
                                <?php } ?>

                                <form method="POST" action="create-alert.php">
                                <div class="panel panel-default" style="border: 2px solid #000;">

                                        <div class="panel-heading">Send Alert to Teachers</div>
                                        <div class="panel-body">

                                            <div class="form-group">
                                                <label for="alertMessage">Alert Message</label>
                                                <textarea name="alertMessage" class="form-control" rows="3" id="alertMessage" required></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="deadline">Deadline</label>
                                                <input type="date" name="deadline" class="form-control" id="deadline" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="teacherId">Send To</label>
                                                <select name="teacherId" class="form-control" id="teacherId" required>
                                                    <option value="all">All Teachers</option>
                                                    <?php
                                                    $sql = "SELECT userId, TeacherName FROM tblteachers";
                                                    $query = $dbh->prepare($sql);
                                                    $query->execute();
                                                    $teachers = $query->fetchAll(PDO::FETCH_OBJ);
                                                    foreach($teachers as $teacher) {
                                                        echo '<option value="'.htmlentities($teacher->userId).'">'.htmlentities($teacher->TeacherName).'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group text-center" style="margin-top: 20px; margin-bottom: 50px;">
    <button type="submit" name="submit" class="btn btn-lg btn-success" style="padding: 10px 30px; font-size: 18px;">
        Send Alert
    </button>
</div>




                                        </div>
                                    </div>
                                </form>

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
