<?php
session_start();
error_reporting(0);
include('includes/config.php');

$msg = "";
$error = "";

// ✅ Correct session check for teacher
if (!isset($_SESSION['tlogin']) || empty($_SESSION['tlogin'])) {   
    header("Location: index.php"); 
    exit();
}

if (isset($_POST['submit'])) {
    $currentPassword = $_POST['password']; // plain text from form
    $newPassword = $_POST['newpassword']; // plain text from form
    $username = $_SESSION['tlogin']; // userId

    // ✅ Step 1: Fetch hashed password from DB
    $sql = "SELECT password FROM teachers WHERE userId = :username";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $hashedPassword = $row['password'];

        // ✅ Step 2: Verify old password
        if (password_verify($currentPassword, $hashedPassword)) {
            // ✅ Step 3: Hash new password and update
            $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $update_sql = "UPDATE teachers SET password = :newpassword WHERE userId = :username";
            $update_query = $dbh->prepare($update_sql);
            $update_query->bindParam(':newpassword', $newHashedPassword, PDO::PARAM_STR);
            $update_query->bindParam(':username', $username, PDO::PARAM_STR);
            $update_query->execute();

            $msg = "Your Password has been successfully changed.";
        } else {
            $error = "Your current password is incorrect!";
        }
    } else {
        $error = "User not found.";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">
    <script>
        function validateForm() {
            let newpassword = document.forms["chngpwd"]["newpassword"].value;
            let confirmpassword = document.forms["chngpwd"]["confirmpassword"].value;
            if (newpassword !== confirmpassword) {
                alert("New Password and Confirm Password do not match!");
                return false;
            }
            return true;
        }
    </script>
    <style>
        .errorWrap { padding: 10px; margin-bottom: 20px; background: #ffdddd; border-left: 4px solid #dd3d36; }
        .succWrap { padding: 10px; margin-bottom: 20px; background: #ddffdd; border-left: 4px solid #5cb85c; }
    </style>
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
                            <h2 class="title">Change Password</h2>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h5>Change Password</h5>
                                    </div>
                                    <?php if ($msg) { ?>
                                        <div class="succWrap"><?php echo htmlentities($msg); ?></div>
                                    <?php } else if ($error) { ?>
                                        <div class="errorWrap"><?php echo htmlentities($error); ?></div>
                                    <?php } ?>

                                    <div class="panel-body">
                                        <form name="chngpwd" method="post" onsubmit="return validateForm();">
                                            <div class="form-group">
                                                <label>Current Password</label>
                                                <input type="password" name="password" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>New Password</label>
                                                <input type="password" name="newpassword" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Confirm Password</label>
                                                <input type="password" name="confirmpassword" class="form-control" required>
                                            </div>
                                            <button type="submit" name="submit" class="btn btn-success">Change Password</button>
                                        </form>
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
</body>
</html>
