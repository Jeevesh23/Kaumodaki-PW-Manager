<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
include_once(__DIR__ . '/config/db.php');
include_once(__DIR__ . '/../authentication/func.php');
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $n_master_pwd = $_POST['reset-password'];
    $salt = getRandomStringRand();
    $hasheddata = hash('sha512', $n_master_pwd . $salt);
    $sql = "UPDATE `Credentials` SET `Password`='$hasheddata',`Salt`='$salt'";
    $req = mysqli_query($con, $sql);
    if (!$req) {
        echo "<script>alert('Updating master password failed!');</script>";
    } else {
        echo "<script>alert('Master Password updated successfully!');</script>";
    }
    $con->close();
    header("Refresh:0.5,url=/vault/settings");
    die();
}
