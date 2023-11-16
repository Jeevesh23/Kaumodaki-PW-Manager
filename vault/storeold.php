<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
$userid = $_SESSION['User_ID'];
$conn = mysqli_connect('db', 'root', 'MYSQL_ROOT_PASSWORD', 'PM_1');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql1 = "SELECT * FROM `Old_Passwords` WHERE `User_ID`='$userid' AND `Website`='" . $_SESSION['website'] . "'";
$res1 = mysqli_query($conn, $sql1);
if (!$res1) {
    header("Refresh:0.5, url=/vault");
    echo '<script>alert("Connection error!")</script>';
    $conn->close();
    exit();
}
if ($res1->num_rows === 5) {
    $sql2 = "DELETE FROM `Old_Passwords` WHERE `User_ID`='$userid' AND `Website`='" . $_SESSION['website'] . "' AND `Add_Date` IS NOT NULL ORDER BY `Add_Date` DESC LIMIT 1";
    $res2 = mysqli_query($conn, $sql2);
    if (!$res2) {
        header("Refresh:0.5, url=/vault");
        echo '<script>alert("Connection error!")</script>';
        $conn->close();
        exit();
    }
}
$link = $_SESSION['link'];
$hash = $_SESSION['hash'];
$website = $_SESSION['website'];
$adddate = $_SESSION['adddate'];
$sql3 = "INSERT INTO `Old_Passwords`(`User_ID`,`Website`,`Link`,`Old_Hash`,`Add_Date`) VALUES ('$userid','$website','$link','$hash','$adddate')";
$res3 = mysqli_query($conn, $sql3);
$conn->close();
if (!$res3) {
    header("Refresh:0.5, url=/vault");
    echo '<script>alert("Connection error!")</script>';
    exit();
} else {
    header("Location:/vault");
    exit();
}
