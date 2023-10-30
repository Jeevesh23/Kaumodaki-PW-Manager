<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
date_default_timezone_set('Asia/Kolkata');
if (isset($_POST['UserID'])) {
    $conn = mysqli_connect('db', 'root', 'MYSQL_ROOT_PASSWORD', 'PM_1');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $desc = $_POST['Description'];
    $user = $_POST['Username'];
    $_SESSION['link'] = $link = $_POST['Link'];
    $sql = "SELECT * FROM `User_Info` WHERE `Link`='$link'";
    $result = mysqli_query($conn, $sql);
    if ($result->num_rows > 0) {
        header("Refresh:3,url=/vault");
        echo ("Website account already exists!");
        exit();
    }
    $key = getenv('AES_KEY');
    $method = "aes-256-cbc";
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $secret = $_POST['Password'];
    $encrypted = openssl_encrypt($secret, $method, $key, iv: $iv);
    $addformat = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
    $_SESSION['adddate'] = $adddate = date("Y-m-d H:i:s", $addformat);
    $_SESSION['hash'] = password_hash($_POST['Password'], PASSWORD_DEFAULT);
    $userid = $_POST['UserID'];
    $sql1 = "INSERT INTO `User_Info` VALUES('$userid','$desc','$user','$link','$encrypted','$iv','$adddate')";
    try {
        $result1 = mysqli_query($conn, $sql1);
        if (!$result1) {
            header("Refresh:3, url= /vault");
            echo "Connection failed!";
            exit();
        } else {
            header("Refresh:3,url= /vault/store-old");
            $conn->close();
            echo "Account successfully registered! Redirecting to vault!";
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        header("Refresh:3, url= /vault");
        echo $e->getMessage();
        exit();
    }
}
