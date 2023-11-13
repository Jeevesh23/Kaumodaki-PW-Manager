<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
date_default_timezone_set('Asia/Kolkata');
if (isset($_POST['User_ID'])) {
    $conn = mysqli_connect('db', 'root', 'MYSQL_ROOT_PASSWORD', 'PM_1');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $website = $_POST['Website'];
    $desc = $_POST['Description'];
    $user = $_POST['Username'];
    $_SESSION['link'] = $link = $_POST['Link'];
    $type = $_POST['Type'];
    $reset = isset($_POST['Reset']) ? 1 : 0;
    $sql = "SELECT * FROM `User_Info` WHERE `Website`='$website'";
    $result = mysqli_query($conn, $sql);
    if ($result->num_rows > 0) {
        header("Refresh:3,url=/vault");
        echo '<script>alert("Website account already exists!")</script>';
        exit();
    }
    $key = getenv('AES_KEY');
    $method = "aes-256-cbc";
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $secret = $_POST['Password'];
    $encrypted = openssl_encrypt($secret, $method, $key, iv: $iv);
    $hexiv = bin2hex($iv);
    $addformat = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
    $_SESSION['website'] = $website;
    $_SESSION['adddate'] = $adddate = date("Y-m-d H:i:s", $addformat);
    $_SESSION['hash'] = password_hash($_POST['Password'], PASSWORD_DEFAULT);
    $userid = $_POST['User_ID'];
    $sql1 = "INSERT INTO `User_Info`(`User_ID`, `Website`, `Description`, `Username`, `Link`, `Password`, `IV`, `Add_Date`, `Wrd/Phr`, `RST`) VALUES('$userid','$website', '$desc','$user','$link','$encrypted','$hexiv','$adddate','$type','$reset')";
    try {
        $result1 = mysqli_query($conn, $sql1);
        if (!$result1) {
            header("Refresh:3, url= /vault");
            echo '<script>alert"Connection failed!"</script>';
            exit();
        } else {
            header("Refresh:3,url= /vault/store-old");
            $conn->close();
            echo '<script>alert"Account successfully registered! Redirecting to vault!"</script>';
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        header("Refresh:3, url= /vault");
        echo $e->getMessage();
        exit();
    }
}
