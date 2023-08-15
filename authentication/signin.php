<?php
session_start();
$conn = mysqli_connect('db', 'root', 'MYSQL_ROOT_PASSWORD', 'PM_1');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];
$otp = $_POST['otp'];
$sql = "SELECT `Salt`,`Password`,`Secret_Key`,`IV` FROM `Credentials` WHERE `Username`='$username'";
$result = mysqli_query($conn, $sql);

if (!$result)
    echo "Connection failed";
else if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hasheddata = hash('sha512', $password . $row["Salt"]);
        if ($row["Password"] == $hasheddata) {
            $key = getenv('AES_KEY');
            $method = "AES-256-CBC";
            $iv = $row["IV"];
            $encrypted = openssl_decrypt($row["Secret_Key"], $method, $key, iv: $iv);
            $_SESSION['secret'] = $encrypted;
            $_SESSION['otp'] = $otp;
            $conn->close();
            header("Location:verify-otp.php");
            exit();
        }
    }
    echo "No account! Register now.";
} else {
    echo "No account! Register now.";
}

$conn->close();

?>