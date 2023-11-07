<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
if (empty($_GET['username']) && empty($_GET['email'])) {
    echo "<script>alert('No data entered!');</script>";
    header("Refresh:0.5,url=/vault/settings");
    die();
}
include_once(__DIR__ . '/config/db.php');
if (!empty($_GET['username'])) {
    $username = $_SESSION['username'] = $_GET['username'];
} else {
    $username = $_SESSION['username'];
}
if (!empty($_GET['email'])) {
    $email = $_SESSION['email'] = $_GET['email'];
} else {
    $email = $_SESSION['email'];
}
$key = getenv('AES_KEY');
$encemail = openssl_encrypt($email, "AES-256-CBC", $key);
$sql = "UPDATE `Credentials` SET `Username`='$username',`Email`='$encemail' WHERE `User_ID`=" . $_SESSION['User_ID'];
$req = mysqli_query($con, $sql);
if (!$req)
    echo "<script>alert('Updating details failed!')</script>";
else
    echo "<script>alert('Details updated successfully!')</script>";
header("Refresh:0.5,url=/vault/settings");
