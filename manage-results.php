<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Initialize messages
$msg = "";
$error = "";

// Role check
$isAdmin = isset($_SESSION['alogin']) && $_SESSION['alogin'] != "";
$isTeacher = isset($_SESSION['tlogin']) && $_SESSION['tlogin'] != "";

// Redirect if neither
if (!$isAdmin && !$isTeacher) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SRMS Manage Results</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen" >
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen" >
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen" >
    <link rel="stylesheet" type="text/css" href="js/DataTables/datatables.min.css"/>
    <link rel="stylesheet" href="css/main.css" media="screen" >
    <script src="js/modernizr/modernizr.min.js"></script>
    <style>
        .errorWrap { padding: 10px; margin: 0 0 20px 0; background: #fff; border-left: 4px solid #dd3d36; box-shadow: 0 1px 1px 0 rgba(0,0,0,.1); }
        .succWrap { padding: 10px; margin: 0 0 20px 0; background: #fff; border-left: 4px solid #5cb85c; box-shadow: 0 1px 1px 0 rgba(0,0,0,.1); }
    </style>
</head>
<body class="top-navbar-fixed">
    <div class="main-wrapper">

        <!-- Top Navbar -->
        <?php include('includes/topbar.php');?>

        <!-- Sidebar -->
        <div class="content-wrapper">
            <div class="content-container">
                <?php
                if ($isAdmin) {
                    include('includes/leftbar.php');
                } elseif ($isTeacher) {
                    include('includes/teacher-leftbar.php');
                }
                ?>

                <!-- Main Page -->//C cCCcxzxc
                <div class="main-page">
                    <div class="container-fluid">
                        <div class="row page-title-div">
                            <div class="col-md-6">
                                <h2 class="title">Manage Results</h2>
                            </div>
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
                                    <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li> Results</li>
                                    <li class="active">Manage Results</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Results Section -->
                    <section class="section">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="panel">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <h5>View Student Results</h5>
                                            </div>
                                        </div>

                                        <?php if ($msg) { ?>
                                            <div class="alert alert-success left-icon-alert">
                                                <strong>Well done!</strong> <?php echo htmlentities($msg); ?>
                                            </div>
                                        <?php } elseif ($error) { ?>
                                            <div class="alert alert-danger left-icon-alert">
                                                <strong>Oh snap!</strong> <?php echo htmlentities($error); ?>
                                            </div>
                                        <?php } ?>

                                        <div class="panel-body p-20">
                                            <table id="example" class="display table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Student Name</th>
                                                        <th>Roll Id</th>
                                                        <th>Class</th>
                                                        <th>Reg Date</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Fetch data
                                                    $sql = "SELECT DISTINCT s.StudentName, s.RollId, s.RegDate, s.StudentId, s.Status, c.ClassName, c.Section 
                                                            FROM tblresult r 
                                                            JOIN tblstudents s ON s.StudentId = r.StudentId 
                                                            JOIN tblclasses c ON c.id = r.ClassId";

                                                    // Optional: filter by teacher's class if required
                                                    if ($isTeacher) {
                                                        // Assuming $_SESSION['teacher_class_id'] is available
                                                        $sql .= " WHERE c.id IN (SELECT ClassId FROM teacher_class_map WHERE TeacherId = :teacherid)";
                                                    }

                                                    $query = $dbh->prepare($sql);
                                                    if ($isTeacher) {
                                                        $query->bindParam(':teacherid', $_SESSION['teacher_id'], PDO::PARAM_INT);
                                                    }
                                                    $query->execute();
                                                    $results = $query->fetchAll(PDO::FETCH_OBJ);

                                                    $cnt = 1;
                                                    if ($query->rowCount() > 0) {
                                                        foreach ($results as $result) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlentities($cnt++); ?></td>
                                                        <td><?php echo htmlentities($result->StudentName); ?></td>
                                                        <td><?php echo htmlentities($result->RollId); ?></td>
                                                        <td><?php echo htmlentities($result->ClassName); ?> (<?php echo htmlentities($result->Section); ?>)</td>
                                                        <td><?php echo htmlentities($result->RegDate); ?></td>
                                                        <td><?php echo $result->Status == 1 ? 'Active' : 'Blocked'; ?></td>
                                                        <td>
                                                            <a href="edit-result.php?stid=<?php echo htmlentities($result->StudentId); ?>">
                                                                <i class="fa fa-edit" title="Edit Record"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php } } ?>
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

    <!-- JS FILES -->
    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/pace/pace.min.js"></script>
    <script src="js/lobipanel/lobipanel.min.js"></script>
    <script src="js/iscroll/iscroll.js"></script>
    <script src="js/DataTables/datatables.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        $(function () {
            $('#example').DataTable();
        });
    </script>
</body>
</html>
