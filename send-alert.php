<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['alogin'])) {
    header('Location: index.php');
    exit();
}

$msg = '';
if (isset($_POST['submit'])) {
    $message = $_POST['alertMessage'];
    $deadline = $_POST['deadline'];

    // Insert a global alert for all teachers (or loop for individual teacherId if needed)
    $sql = "INSERT INTO teacher_alerts (alertMessage, deadline) VALUES (:msg, :deadline)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':msg', $message, PDO::PARAM_STR);
    $query->bindParam(':deadline', $deadline, PDO::PARAM_STR);
    $query->execute();

    $msg = "Alert successfully sent!";
}
?>

<!DOCTYPE html>
<html>
<head><title>Send Alert</title></head>
<body>
<h2>Send Alert to Teachers</h2>
<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<form method="post">
    <textarea name="alertMessage" placeholder="Enter alert message" required></textarea><br><br>
    <label>Deadline: </label>
    <input type="date" name="deadline" required><br><br>
    <input type="submit" name="submit" value="Send Alert">
</form>
</body>
</html>
