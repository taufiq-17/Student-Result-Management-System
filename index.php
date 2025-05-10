<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Admin Login
if(isset($_POST['login'])) {
    $uname = $_POST['username'];
    $password = md5($_POST['password']);
    
    // Fetch UserName, Password, and Role
    $sql = "SELECT UserName, Password, Role FROM admin WHERE UserName=:uname AND Password=:password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uname', $uname, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
    if($result) {
        $_SESSION['alogin'] = $result['UserName'];
        $_SESSION['role'] = $result['Role']; // Store role in session

        echo "<script type='text/javascript'> document.location = 'dashboard.php'; </script>";
    } else {
        echo "<script>alert('Invalid Admin Details');</script>";
    }
}


// Teacher Login
if(isset($_POST['tlogin'])) {
    $tuname = $_POST['tusername'];
    $tpassword = $_POST['tpassword']; // Get the raw input password
    
    // Fetch stored hashed password
    $sql = "SELECT userId, password FROM teachers WHERE userId=:username";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $tuname, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Verify password
        if (password_verify($tpassword, $result['password'])) {
            $_SESSION['tlogin'] = $tuname;
            echo "<script type='text/javascript'> document.location = 'dashboardt.php'; </script>";
        } else {
            echo "<script>alert('Invalid Password');</script>";
        }
    } else {
        echo "<script>alert('Invalid User ID');</script>";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Student Result Management System</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/main.css">
    </head>
    <body>
        <div class="main-wrapper">
            <div class="row">
                <h1 align="center">Student Result Management System</h1>

                <!-- Student Login -->
                <div class="col-lg-4">
                    <section class="section">
                        <div class="panel">
                            <div class="panel-heading text-center">
                                <h4>For Students</h4>
                            </div>
                            <div class="panel-body">
                                <p class="sub-title text-center">Search your result</p>
                                <div class="text-center">
                                    <a href="find-result.php" class="btn btn-primary">Click Here</a>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Admin Login -->
                <div class="col-lg-4">
                    <section class="section">
                        <div class="panel">
                            <div class="panel-heading text-center">
                                <h4>Admin Login</h4>
                            </div>
                            <div class="panel-body">
                                <form method="post">
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control" placeholder="Username">
                                    </div>
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Password">
                                    </div>
                                    <button type="submit" name="login" class="btn btn-success">Sign in</button>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Teacher Login -->
                <div class="col-lg-4">
                    <section class="section">
                        <div class="panel">
                            <div class="panel-heading text-center">
                                <h4>Teacher Login</h4>
                            </div>
                            <div class="panel-body">
                                <form method="post">
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" name="tusername" class="form-control" placeholder="Username">
                                    </div>
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="tpassword" class="form-control" placeholder="Password">
                                    </div>
                                    <button type="submit" name="tlogin" class="btn btn-info">Sign in</button>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <script src="js/jquery/jquery-2.2.4.min.js"></script>
        <script src="js/bootstrap/bootstrap.min.js"></script>
    </body>
</html>
