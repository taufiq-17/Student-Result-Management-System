<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
} else {
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SRMS Admin Manage Teachers</title>
        <link rel="stylesheet" href="css/bootstrap.min.css" media="screen">
        <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">
        <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen">
        <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen">
        <link rel="stylesheet" type="text/css" href="js/DataTables/datatables.min.css"/>
        <link rel="stylesheet" href="css/main.css" media="screen">
        <script src="js/modernizr/modernizr.min.js"></script>
    </head>
    <body class="top-navbar-fixed">
        <div class="main-wrapper">
            <?php include('includes/topbar.php');?> 
            <div class="content-wrapper">
                <div class="content-container">
                    <?php include('includes/leftbar.php');?>  
                    <div class="main-page">
                        <div class="container-fluid">
                            <div class="row page-title-div">
                                <div class="col-md-6">
                                    <h2 class="title">Manage Teachers</h2>
                                </div>
                            </div>
                            <div class="row breadcrumb-div">
                                <div class="col-md-6">
                                    <ul class="breadcrumb">
                                        <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                        <li> Teachers</li>
                                        <li class="active">Manage Teachers</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <section class="section">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel">
                                            <div class="panel-heading">
                                                <div class="panel-title">
                                                    <h5>View Teachers Info</h5>
                                                </div>
                                            </div>
                                            <div class="panel-body p-20">
                                                <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Teacher Name</th>
                                                            <th>Teacher ID</th>
                                                            <th>Email</th>
                                                            <th>Phone</th>
                                                            <th>Gender</th>
                                                            <th>Classes</th>
                                                            <th>Subjects Taught</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
<?php 
$sql = "SELECT * FROM teachers";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
$cnt = 1;
if($query->rowCount() > 0) {
    foreach($results as $result) {   
?>
<tr>
    <td><?php echo htmlentities($cnt); ?></td>
    <td><?php echo htmlentities($result->name ?? 'N/A'); ?></td>
    <td><?php echo htmlentities($result->userId ?? 'N/A'); ?></td>
    <td><?php echo htmlentities($result->emailID ?? 'N/A'); ?></td>
    <td><?php echo htmlentities($result->phoneNo ?? 'N/A'); ?></td>
    <td><?php echo htmlentities($result->gender ?? 'N/A'); ?></td>
    <td><?php echo htmlentities($result->classAssigned ?? 'N/A'); ?></td>
    <td><?php echo htmlentities($result->subjectsTaught ?? 'N/A'); ?></td>
    <td>
        <?php if(!empty($result->userId)): ?>
            <a href="edit-teacher.php?tid=<?php echo htmlentities($result->userId); ?>">
                <i class="fa fa-edit" title="Edit Record"></i>
            </a>
        <?php endif; ?>
    </td>
</tr>
<?php 
        $cnt++;
    } 
} else {
    echo '<tr><td colspan="9" class="text-center">No teachers found</td></tr>';
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
        <script src="js/jquery/jquery-2.2.4.min.js"></script>
        <script src="js/bootstrap/bootstrap.min.js"></script>
        <script src="js/DataTables/datatables.min.js"></script>
        <script src="js/main.js"></script>
        <script>
            $(function($) {
                $('#example').DataTable();
            });
        </script>
    </body>
</html>
<?php } ?>
