<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    session_start();
    $conn = mysqli_connect('db', 'root', 'MYSQL_ROOT_PASSWORD', 'PM_1');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $_SESSION['email'] = $_POST['email'];
    $hashemail = hash('md5', $_POST['email']);
    $_SESSION['hashemail'] = $hashemail;

    $key = getenv('AES_KEY');
    $method = "AES-256-CBC";
    $encemail = openssl_encrypt($_POST['email'], $method, $key);

    $password = $_POST['password'];
    $otp = $_POST['otp'];
    $sql = "SELECT `Salt`,`Password`,`Secret_Key`,`IV`,`User_ID`,`Salt2`,`Username` FROM `Credentials` WHERE `Email`='$encemail'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        header("Refresh:3, url= /authentication");
        echo '<script>alert("Connection failed")</script>';
        $conn->close();
        exit();
    } else if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $hasheddata = hash('sha512', $password . $row["Salt"]);
            if ($row["Password"] == $hasheddata) {
                $iv = $row["IV"];
                $encrypted = openssl_decrypt($row["Secret_Key"], $method, $key, iv: hex2bin($iv));
                $_SESSION['secret'] = $encrypted;
                $_SESSION['otp'] = $otp;
                $_SESSION['User_ID'] = $row['User_ID'];
                $_SESSION['Username'] = $row['Username'];
                $salt2 = $_SESSION['salt2'] = $row['Salt2'];
                $_SESSION['pwdkey'] = $pwdkey = hash_pbkdf2("sha512", $password, $salt2, 500000, 64);
                setcookie($_SESSION['hashemail'], 'signin', time() + 360, path: '/');
                $conn->close();
                header("Location:/authentication/verify-otp");
                exit();
            }
        }
        echo '<script>alert("Wrong credentials!")</script>';
    } else {
        echo '<script>alert("No account! Register now.")</script>';
    }

    $conn->close();
    header("Refresh:0.5, url= /authentication");
} else {
    header("Location: /authentication");
    exit();
}
