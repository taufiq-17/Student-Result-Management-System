<?php
session_start();
error_reporting(0);
include('includes/config.php');

// 🔹 Auth check
if(strlen($_SESSION['alogin']) == "") {   
    header("Location: index.php"); 
    exit();
}

$msg = "";
$error = "";
$teacher = null;

if (isset($_POST['update'])) {
    $userId = $_POST['userId'];
    $name = $_POST['name'];
    $email = $_POST['emailID'];
    $phone = $_POST['phoneNo'];
    $gender = $_POST['gender'];
    $classes = $_POST['classes'];
    $subjects = $_POST['subjects'];
    $password = $_POST['password'];

    // 🔸 Password update only if provided
    if (!empty($password)) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE teachers 
                SET name = :name, emailID = :email, phoneNo = :phone, gender = :gender, 
                    classAssigned = :classes, subjectsTaught = :subjects, password = :password 
                WHERE userId = :userId";
    } else {
        $sql = "UPDATE teachers 
                SET name = :name, emailID = :email, phoneNo = :phone, gender = :gender, 
                    classAssigned = :classes, subjectsTaught = :subjects 
                WHERE userId = :userId";
    }

    $query = $dbh->prepare($sql);
    $query->bindParam(':name', $name, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':phone', $phone, PDO::PARAM_STR);
    $query->bindParam(':gender', $gender, PDO::PARAM_STR);
    $query->bindParam(':classes', $classes, PDO::PARAM_STR);
    $query->bindParam(':subjects', $subjects, PDO::PARAM_STR);
    $query->bindParam(':userId', $userId, PDO::PARAM_STR);

    if (!empty($password)) {
        $query->bindParam(':password', $passwordHash, PDO::PARAM_STR);
    }

    if ($query->execute()) {
        $msg = "Teacher details updated successfully!";
    } else {
        $error = "Something went wrong. Please try again.";
    }
}

// 🔸 Fetch teacher info for pre-filling
if (isset($_GET['tid'])) {
    $userId = $_GET['tid'];
    $sql = "SELECT * FROM teachers WHERE userId = :userId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':userId', $userId, PDO::PARAM_STR);
    $query->execute();
    $teacher = $query->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SMS Admin | Edit Teacher</title>
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
                    <h2 class="title">Edit Teacher Details</h2>

                    <!-- 🔹 Display Success/Error Messages -->
                    <?php if (!empty($msg)) { ?>
                        <div class="alert alert-success"><strong>Success!</strong> <?php echo htmlentities($msg); ?></div>
                    <?php } elseif (!empty($error)) { ?>
                        <div class="alert alert-danger"><strong>Error!</strong> <?php echo htmlentities($error); ?></div>
                    <?php } ?>

                    <div class="panel">
                        <div class="panel-heading">
                            <h5>Fill in the Teacher Info</h5>
                        </div>
                        <div class="panel-body">
                            <?php if ($teacher) { ?>
                                <form class="form-horizontal" method="post">
                                    <input type="hidden" name="userId" value="<?php echo htmlentities($teacher['userId']); ?>">

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Full Name</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="name" class="form-control" value="<?php echo htmlentities($teacher['name']); ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Email</label>
                                        <div class="col-sm-10">
                                            <input type="email" name="emailID" class="form-control" value="<?php echo htmlentities($teacher['emailID']); ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Phone No</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="phoneNo" class="form-control" value="<?php echo htmlentities($teacher['phoneNo']); ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Gender</label>
                                        <div class="col-sm-10">
                                            <label><input type="radio" name="gender" value="Male" <?php if ($teacher['gender'] == 'Male') echo 'checked'; ?>> Male</label>
                                            <label><input type="radio" name="gender" value="Female" <?php if ($teacher['gender'] == 'Female') echo 'checked'; ?>> Female</label>
                                            <label><input type="radio" name="gender" value="Other" <?php if ($teacher['gender'] == 'Other') echo 'checked'; ?>> Other</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Assigned Classes</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="classes" class="form-control" value="<?php echo htmlentities($teacher['classAssigned']); ?>">
                                            <small>Separate multiple classes with comma (e.g. 1, 2, 3)</small>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Subjects Taught</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="subjects" class="form-control" value="<?php echo htmlentities($teacher['subjectsTaught']); ?>">
                                            <small>Separate multiple subjects with comma (e.g. Math, English)</small>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">New Password</label>
                                        <div class="col-sm-10">
                                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" name="update" class="btn btn-warning">Update</button>
                                        </div>
                                    </div>
                                </form>
                            <?php } else { ?>
                                <div class="alert alert-danger">Teacher not found!</div>
                            <?php } ?>
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
