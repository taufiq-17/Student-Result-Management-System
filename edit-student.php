<?php
session_start();
error_reporting(0);
include('includes/config.php');

// 🔹 Initialize variables to prevent "Undefined variable" warnings
$msg = "";
$error = "";

if(strlen($_SESSION['alogin']) == "") {   
    header("Location: index.php"); 
} else {

$stid = intval($_GET['stid']);

if(isset($_POST['submit'])) {
    $studentname = $_POST['fullanme'];
    $roolid = $_POST['rollid']; 
    $studentemail = $_POST['emailid']; 
    $gender = $_POST['gender']; 
    $dob = $_POST['dob']; 
    $status = $_POST['status'];

    $sql = "UPDATE tblstudents SET StudentName=:studentname, RollId=:roolid, StudentEmail=:studentemail, Gender=:gender, DOB=:dob, Status=:status WHERE StudentId=:stid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':studentname', $studentname, PDO::PARAM_STR);
    $query->bindParam(':roolid', $roolid, PDO::PARAM_STR);
    $query->bindParam(':studentemail', $studentemail, PDO::PARAM_STR);
    $query->bindParam(':gender', $gender, PDO::PARAM_STR);
    $query->bindParam(':dob', $dob, PDO::PARAM_STR);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->bindParam(':stid', $stid, PDO::PARAM_STR);

    if($query->execute()) {
        $msg = "Student info updated successfully!";
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SMS Admin | Edit Student</title>
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
                        <h2 class="title">Edit Student Details</h2>
                        
                        <!-- 🔹 Display Success/Error Messages -->
                        <?php if (!empty($msg)) { ?>
                            <div class="alert alert-success"><strong>Success!</strong> <?php echo htmlentities($msg); ?></div>
                        <?php } elseif (!empty($error)) { ?>
                            <div class="alert alert-danger"><strong>Error!</strong> <?php echo htmlentities($error); ?></div>
                        <?php } ?>

                        <div class="panel">
                            <div class="panel-heading">
                                <h5>Fill in the Student Info</h5>
                            </div>
                            <div class="panel-body">
                                <form class="form-horizontal" method="post">
                                    <?php 
                                    $sql = "SELECT StudentName, RollId, StudentEmail, Gender, DOB, Status FROM tblstudents WHERE StudentId=:stid";
                                    $query = $dbh->prepare($sql);
                                    $query->bindParam(':stid', $stid, PDO::PARAM_STR);
                                    $query->execute();
                                    $result = $query->fetch(PDO::FETCH_OBJ);
                                    
                                    if($result) { ?>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Full Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="fullanme" class="form-control" value="<?php echo htmlentities($result->StudentName); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Roll ID</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="rollid" class="form-control" value="<?php echo htmlentities($result->RollId); ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Email ID</label>
                                            <div class="col-sm-10">
                                                <input type="email" name="emailid" class="form-control" value="<?php echo htmlentities($result->StudentEmail); ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Gender</label>
                                            <div class="col-sm-10">
                                                <input type="radio" name="gender" value="Male" <?php echo ($result->Gender == "Male") ? "checked" : ""; ?>> Male
                                                <input type="radio" name="gender" value="Female" <?php echo ($result->Gender == "Female") ? "checked" : ""; ?>> Female
                                                <input type="radio" name="gender" value="Other" <?php echo ($result->Gender == "Other") ? "checked" : ""; ?>> Other
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">DOB</label>
                                            <div class="col-sm-10">
                                                <input type="date" name="dob" class="form-control" value="<?php echo htmlentities($result->DOB); ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Status</label>
                                            <div class="col-sm-10">
                                                <input type="radio" name="status" value="1" <?php echo ($result->Status == "1") ? "checked" : ""; ?>> Active
                                                <input type="radio" name="status" value="0" <?php echo ($result->Status == "0") ? "checked" : ""; ?>> Blocked
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button type="submit" name="submit" class="btn btn-warning">Update</button>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="alert alert-danger">Student not found!</div>
                                    <?php } ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="js/jquery/jquery-2.2.4.min.js"></script>
        <script src="js/bootstrap/bootstrap.min.js"></script>
    </div>
</body>
</html>
<?php } ?>
