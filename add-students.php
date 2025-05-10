<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
} else {
    // ✅ Initialize the variables to prevent undefined variable warnings
    $msg = "";
    $error = "";

    if(isset($_POST['submit'])) {
        $studentname = $_POST['fullanme'];
        $roolid = $_POST['rollid']; 
        $studentemail = $_POST['emailid']; 
        $gender = $_POST['gender']; 
        $classid = $_POST['class']; 
        $dob = $_POST['dob']; 
        $status = 1;

        $sql = "INSERT INTO tblstudents (StudentName, RollId, StudentEmail, Gender, ClassId, DOB, Status) 
                VALUES (:studentname, :roolid, :studentemail, :gender, :classid, :dob, :status)";
        
        $query = $dbh->prepare($sql);
        $query->bindParam(':studentname', $studentname, PDO::PARAM_STR);
        $query->bindParam(':roolid', $roolid, PDO::PARAM_STR);
        $query->bindParam(':studentemail', $studentemail, PDO::PARAM_STR);
        $query->bindParam(':gender', $gender, PDO::PARAM_STR);
        $query->bindParam(':classid', $classid, PDO::PARAM_STR);
        $query->bindParam(':dob', $dob, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->execute();

        $lastInsertId = $dbh->lastInsertId();
        if($lastInsertId) {
            $msg = "Student info added successfully";
        } else {
            $error = "Something went wrong. Please try again";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SRMS Admin | Student Admission</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
    <link rel="stylesheet" href="css/main.css" media="screen">
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
                                <h2 class="title">Student Admission</h2>
                            </div>
                        </div>

                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <h5>Fill the Student Info</h5>
                                        </div>
                                        <div class="panel-body">
                                            <?php if($msg) { ?>
                                                <div class="alert alert-success">
                                                    <strong>Success!</strong> <?php echo htmlentities($msg); ?>
                                                </div>
                                            <?php } else if($error) { ?>
                                                <div class="alert alert-danger">
                                                    <strong>Error!</strong> <?php echo htmlentities($error); ?>
                                                </div>
                                            <?php } ?>

                                            <form class="form-horizontal" method="post">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Full Name</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="fullanme" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Roll Id</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="rollid" class="form-control" maxlength="5" required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Email Id</label>
                                                    <div class="col-sm-10">
                                                        <input type="email" name="emailid" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Gender</label>
                                                    <div class="col-sm-10">
                                                        <input type="radio" name="gender" value="Male" required checked> Male 
                                                        <input type="radio" name="gender" value="Female" required> Female 
                                                        <input type="radio" name="gender" value="Other" required> Other
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Class</label>
                                                    <div class="col-sm-10">
                                                        <select name="class" class="form-control" required>
                                                            <option value="">Select Class</option>
                                                            <?php 
                                                            $sql = "SELECT * FROM tblclasses";
                                                            $query = $dbh->prepare($sql);
                                                            $query->execute();
                                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                            if($query->rowCount() > 0) {
                                                                foreach($results as $result) { ?>
                                                                    <option value="<?php echo htmlentities($result->id); ?>">
                                                                        <?php echo htmlentities($result->ClassName); ?> Section-<?php echo htmlentities($result->Section); ?>
                                                                    </option>
                                                                <?php } 
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">DOB</label>
                                                    <div class="col-sm-10">
                                                        <input type="date" name="dob" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-sm-offset-2 col-sm-10">
                                                        <button type="submit" name="submit" class="btn btn-primary">Add</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- Container -->
                    </div> <!-- Main Page -->
                </div> <!-- Content Container -->
            </div> <!-- Content Wrapper -->
        </div> <!-- Main Wrapper -->
        
        <script src="js/jquery/jquery-2.2.4.min.js"></script>
        <script src="js/bootstrap/bootstrap.min.js"></script>
    </body>
</html>
<?php } ?>
