<?php
session_start();
if (!isset($_POST['UserID'])) { ?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Password Vault</title>
    </head>

    <body>
        <form method="post" action="">
            <input type="text" name="Description" placeholder="Description" required></input>
            <br><br>
            <input type="url" name="Link" placeholder="Link" required></input>
            <br><br>
            <input type="password" name="Password" placeholder="Password" required></input>
            <br><br>
            <input type="hidden" name="UserID" required value="<?php echo "11"; ?>"></input>
            <input type="submit" value="Send Request"></input>
    </body>

    </html>
<?php } else if (isset($_POST['UserID'])) {
    $conn = mysqli_connect('db', 'root', 'MYSQL_ROOT_PASSWORD', 'PM_1');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $desc = $_POST['Description'];
    $link = $_POST['Link'];
    $sql = "SELECT * FROM `User_Info` WHERE `Link`='$link'";
    $result = mysqli_query($conn, $sql);
    if ($result->num_rows > 0) {
        header("Refresh:3,url=index.php");
        echo ("Website account already exists!");
    }
    $key = getenv('AES_KEY');
    $method = "aes-256-cbc";
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $secret = $_POST['Password'];
    $encrypted = openssl_encrypt($secret, $method, $key, iv: $iv);
    $userid = $_POST['UserID'];
    $sql1 = "INSERT INTO `User_Info` VALUES('$userid','$desc','$link','$encrypted','$iv')";
    try {
        $result1 = mysqli_query($conn, $sql1);
        if (!$result1) {
            header("Refresh:3, url= index.php");
            echo "Connection failed!";
            exit();
        } else {
            header("Refresh:3,url= index.php");
            $conn->close();
            echo "Account successfully registered! Redirecting to vault!";
        }
    } catch (mysqli_sql_exception $e) {
        header("Refresh:3, url= index.php");
        echo $e->getMessage();
    }
}
?>