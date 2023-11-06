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
    $sql = "SELECT `Salt`,`Password`,`Secret_Key`,`IV`,`User_ID`,`Username` FROM `Credentials` WHERE `Email`='$encemail'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        header("Refresh:3, url= /authentication");
        echo "Connection failed";
        $conn->close();
        exit();
    } else if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $hasheddata = hash('sha512', $password . $row["Salt"]);
            if ($row["Password"] == $hasheddata) {
                $iv = $row["IV"];
                $encrypted = openssl_decrypt($row["Secret_Key"], $method, $key, iv: $iv);
                $_SESSION['secret'] = $encrypted;
                $_SESSION['otp'] = $otp;
                $_SESSION['User_ID'] = $row['User_ID'];
                $_SESSION['Username'] = $row['Username'];
                setcookie($_SESSION['hashemail'], 'signin', time() + 360, path: '/');
                $conn->close();
                header("Location:/authentication/verify-otp");
                exit();
            }
        }
        echo "Wrong credentials!";
    } else {
        echo "No account! Register now.";
    }

    $conn->close();
} else {
    header("Location: /authentication");
    exit();
}
