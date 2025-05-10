<?php
session_start();
error_reporting(0);
include('includes/config.php');

$msg = "";
$error = "";

if (!isset($_SESSION['tlogin']) || $_SESSION['tlogin'] == "") {
    header("Location: index.php");
    exit();
}

$teacherId = $_SESSION['tlogin'];
$classAssigned = "";
$teacherName = "";

// Fetch teacher info
$sql = "SELECT name, classAssigned FROM teachers WHERE userId = :tid";
$query = $dbh->prepare($sql);
$query->bindParam(':tid', $teacherId, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);

if ($result) {
    // Get all class IDs assigned to this teacher
$classIds = [];
$sqlClasses = "SELECT classId FROM teacher_class_map WHERE teacherId = :tid";
$queryClasses = $dbh->prepare($sqlClasses);
$queryClasses->bindParam(':tid', $teacherId, PDO::PARAM_STR);
$queryClasses->execute();
$assignedClasses = $queryClasses->fetchAll(PDO::FETCH_OBJ);

if ($assignedClasses) {
    foreach ($assignedClasses as $row) {
        $classIds[] = $row->classId;
    }
} else {
    $error = "No classes assigned to this teacher.";
}

    $teacherName = $result->name;
} else {
    $error = "Teacher record not found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SRMS | Teacher Manage Results</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/datatables.min.css">
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
                            <h2 class="title">Manage Results - <?php echo htmlentities($teacherName); ?> (<?php echo htmlentities($classAssigned); ?>)</h2>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="container-fluid">
                        <?php if($msg){?>
                            <div class="alert alert-success"> <strong>Success: </strong><?php echo htmlentities($msg); ?></div>
                        <?php } elseif($error){?>
                            <div class="alert alert-danger"> <strong>Error: </strong><?php echo htmlentities($error); ?></div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h5>View Students Info</h5>
                                    </div>
                                    <div class="panel-body">
                                        <table id="example" class="table table-striped table-bordered">
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
                                           if (!empty($classIds)) {
                                            $inQuery = implode(',', array_fill(0, count($classIds), '?'));
                                        
                                            $sql = "SELECT DISTINCT 
                                                        tblstudents.StudentName, 
                                                        tblstudents.RollId, 
                                                        tblstudents.RegDate, 
                                                        tblstudents.StudentId, 
                                                        tblstudents.Status, 
                                                        tblclasses.ClassName, 
                                                        tblclasses.Section 
                                                    FROM tblresult 
                                                    JOIN tblstudents ON tblstudents.StudentId = tblresult.StudentId 
                                                    JOIN tblclasses ON tblclasses.id = tblresult.ClassId 
                                                    WHERE tblresult.ClassId IN ($inQuery)";
                                        
                                            $query = $dbh->prepare($sql);
                                            
                                            foreach ($classIds as $k => $id) {
                                                $query->bindValue(($k+1), $id, PDO::PARAM_INT);
                                            }
                                        
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        }
                                        
                                            $query->execute();
                                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                                            $cnt=1;
                                            if($query->rowCount() > 0) {
                                                foreach($results as $result) {
                                            ?>
                                            <tr>
                                                <td><?php echo htmlentities($cnt);?></td>
                                                <td><?php echo htmlentities($result->StudentName);?></td>
                                                <td><?php echo htmlentities($result->RollId);?></td>
                                                <td><?php echo htmlentities($result->ClassName);?> (<?php echo htmlentities($result->Section);?>)</td>
                                                <td><?php echo htmlentities($result->RegDate);?></td>
                                                <td><?php echo htmlentities($result->Status ? 'Active' : 'Blocked'); ?></td>
                                                <td><a href="edit-result.php?stid=<?php echo htmlentities($result->StudentId);?>"><i class="fa fa-edit"></i></a></td>
                                            </tr>
                                            <?php $cnt++; } } ?>
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
<script src="js/datatables.min.js"></script>
<script>
    $(function () {
        $('#example').DataTable();
    });
</script>
</body>
</html>
