<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/config.php');

if (strlen($_SESSION['alogin']) == "") {   
    header("Location: index.php");
    exit;
}

$msg = "";
$error = "";

if (isset($_POST['submit'])) {
    $userId = $_POST['userId'];
    $teachername = $_POST['fullname'];
    $teacheremail = $_POST['emailid'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];

    $classes = '';
    if (!empty($_POST['classes'])) {
        $classNames = [];
        foreach ($_POST['classes'] as $classId) {
            $stmt = $dbh->prepare("SELECT ClassName, Section FROM tblclasses WHERE id = :id");
            $stmt->bindParam(':id', $classId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $classNames[] = $row['ClassName'] . ' - ' . $row['Section'];
            }
        }
        $classes = implode(", ", $classNames);
    }

    $subjects = '';
    if (!empty($_POST['subjects'])) {
        $subjectNames = [];
        foreach ($_POST['subjects'] as $subjectId) {
            $stmt = $dbh->prepare("SELECT SubjectName FROM tblsubjects WHERE id = :id");
            $stmt->bindParam(':id', $subjectId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $subjectNames[] = $row['SubjectName'];
            }
        }
        $subjects = implode(", ", $subjectNames);
    }

    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $checkSql = "SELECT COUNT(*) FROM teachers WHERE emailID = :email OR phoneNo = :phone OR userId = :userId";
        $checkQuery = $dbh->prepare($checkSql);
        $checkQuery->bindParam(':email', $teacheremail, PDO::PARAM_STR);
        $checkQuery->bindParam(':phone', $phone, PDO::PARAM_STR);
        $checkQuery->bindParam(':userId', $userId, PDO::PARAM_STR);
        $checkQuery->execute();
        $count = $checkQuery->fetchColumn();

        if ($count > 0) {
            $_SESSION['error_msg'] = "User ID, Email, or Phone Number already exists!";
        } else {
            $sql = "INSERT INTO teachers (userId, name, emailID, phoneNo, gender, classAssigned, subjectsTaught, password)
                    VALUES (:userId, :name, :email, :phone, :gender, :class_assigned, :subjects_assigned, :password)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':userId', $userId, PDO::PARAM_STR);
            $query->bindParam(':name', $teachername, PDO::PARAM_STR);
            $query->bindParam(':email', $teacheremail, PDO::PARAM_STR);
            $query->bindParam(':phone', $phone, PDO::PARAM_STR);
            $query->bindParam(':gender', $gender, PDO::PARAM_STR);
            $query->bindParam(':class_assigned', $classes, PDO::PARAM_STR);
            $query->bindParam(':subjects_assigned', $subjects, PDO::PARAM_STR);
            $query->bindParam(':password', $password, PDO::PARAM_STR);

            if ($query->execute()) {
                // Map classes
                if (!empty($_POST['classes'])) {
                    foreach ($_POST['classes'] as $classId) {
                        $insertMap = $dbh->prepare("INSERT INTO teacher_class_map (teacherId, classid) VALUES (:userId, :classId)");
                        $insertMap->bindParam(':userId', $userId, PDO::PARAM_STR);
                        $insertMap->bindParam(':classId', $classId, PDO::PARAM_INT);
                        $insertMap->execute();
                    }
                }

                // Map subjects
                if (!empty($_POST['subjects'])) {
                    foreach ($_POST['subjects'] as $subjectId) {
                        $insertSubMap = $dbh->prepare("INSERT INTO teacher_subject_map (teacherId, subjectId) VALUES (:userId, :subjectId)");
                        $insertSubMap->bindParam(':userId', $userId, PDO::PARAM_STR);
                        $insertSubMap->bindParam(':subjectId', $subjectId, PDO::PARAM_INT);
                        $insertSubMap->execute();
                    }
                }

                $_SESSION['success_msg'] = "Teacher added and mapped successfully!";
                header("Location: add-teacher.php");
                exit;
            } else {
                $_SESSION['error_msg'] = "Failed to add teacher!";
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SRMS Admin | Add Teacher</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="main-wrapper">
        <?php include('includes/topbar.php'); ?>
        <div class="content-wrapper">
            <div class="content-container">
                <?php include('includes/leftbar.php'); ?>
                <div class="main-page">
                    <div class="container-fluid">
                        <div class="row page-title-div">
                            <div class="col-md-6">
                                <h2 class="title">Add Teacher</h2>
                            </div>
                        </div>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <h5>Fill in the Teacher's Details</h5>
                                        </div>
                                        <div class="panel-body">
                                            <?php if (!empty($_SESSION['success_msg'])) { ?>
                                                <div class="alert alert-success">
                                                    <strong>Success!</strong> <?php echo htmlentities($_SESSION['success_msg']); ?>
                                                </div>
                                                <?php unset($_SESSION['success_msg']); ?>
                                            <?php } ?>
                                            
                                            <?php if (!empty($_SESSION['error_msg'])) { ?>
                                                <div class="alert alert-danger">
                                                    <strong>Error!</strong> <?php echo htmlentities($_SESSION['error_msg']); ?>
                                                </div>
                                                <?php unset($_SESSION['error_msg']); ?>
                                            <?php } ?>

                                            <form class="form-horizontal" method="post">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">User ID</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="userId" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Full Name</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="fullname" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Email ID</label>
                                                    <div class="col-sm-10">
                                                        <input type="email" name="emailid" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Phone Number</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="phone" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Gender</label>
                                                    <div class="col-sm-10">
                                                        <select name="gender" class="form-control" required>
                                                            <option value="">Select Gender</option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Assign Classes -->
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Assign Classes</label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                        $sql = "SELECT * FROM tblclasses";
                                                        $query = $dbh->prepare($sql);
                                                        $query->execute();
                                                        $classes = $query->fetchAll(PDO::FETCH_ASSOC);

                                                        foreach ($classes as $class) {
                                                            echo '<input type="checkbox" name="classes[]" value="' . $class['id'] . '"> ' . $class['ClassName'] . ' - ' . $class['Section'] . '<br>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                                <!-- Assign Subjects -->
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Assign Subjects</label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                        $sql = "SELECT * FROM tblsubjects";
                                                        $query = $dbh->prepare($sql);
                                                        $query->execute();
                                                        $subjects = $query->fetchAll(PDO::FETCH_ASSOC);

                                                        foreach ($subjects as $subject) {
                                                            echo '<input type="checkbox" name="subjects[]" value="' . $subject['id'] . '"> ' . $subject['SubjectName'] . '<br>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                                <!-- Password -->
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Password</label>
                                                    <div class="col-sm-10">
                                                        <input type="password" name="password" class="form-control" required>
                                                    </div>
                                                </div>

                                                <button type="submit" name="submit" class="btn btn-primary">Add Teacher</button>
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
    </div>
</body>
</html>
