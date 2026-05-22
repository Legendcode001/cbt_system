<?php
session_start();

// Check if teacher_id exists. If not, they aren't logged in.
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../login.php");
    exit(); // Always use exit() after a header redirect!
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="assets/images/launcher_iconn.png" type="image/png">
</head>

<body>

    <div class="topbar">
        RECTEM CBT — Teacher Panel
        <div style="float:right;">
            Welcome, <?php echo $_SESSION['name']; ?> | <a href="../logout.php" style="color:white;">Logout</a>
        </div>
    </div>