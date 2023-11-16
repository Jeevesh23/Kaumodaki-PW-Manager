<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}

$website = $_POST["edit"];
$userid = $_SESSION['User_ID'];

$db_host = "db";
$db_username = "root";
$db_password = "MYSQL_ROOT_PASSWORD";
$db_name = "PM_1";

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$pwdkey = $_SESSION['pwdkey'];
$sql = "SELECT `Website`,`Link`,`Username`,`Password`,`Description`,`Wrd/Phr`,`RST`,`IV` FROM `User_Info` WHERE `User_ID` = $userid AND `Website` = '$website'";
$result = $conn->query($sql);
$data = array();

if ($result->num_rows == 1) {
    while ($row = $result->fetch_assoc()) {
        foreach ($row as $key => $value) {
            if ($key !== 'IV') {
                $data[$key] = $value;
            }
        }
        $data['DecPwd'] = openssl_decrypt($row['Password'], "AES-256-CBC", $pwdkey, iv: hex2bin($row['IV']));
    }
    echo json_encode($data, JSON_THROW_ON_ERROR);
} else {
    echo json_encode(['error' => 'Password not found or unauthorized access']);
}
$conn->close();
