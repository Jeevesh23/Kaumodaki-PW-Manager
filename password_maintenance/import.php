<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    $servername = "db";
    $username = "root";
    $password = "MYSQL_ROOT_PASSWORD";
    $dbname = "PM_1";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $dec_key = getenv('AES_KEY');
    $query = "SELECT * FROM `User_Info` WHERE `User_ID`=" . $_SESSION['User_ID'];

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $filename = "/var/www/html/password_maintenance/sql/User_Table.sql";
        $file = fopen($filename, "w");

        while ($row = $result->fetch_assoc()) {
            $sqlData = "INSERT INTO `User_Table` (`Website`, `Username`, `Link`, `Password`, `Add_Date`, `Description`, `Wrd/Phr`) VALUES ('" . $row["Website"] . "', '" . $row["Username"] . "', '" . $row["Link"] . "','" . openssl_decrypt($row["Password"], "AES-256-CBC", $dec_key, iv: $row["IV"]) . "', '" . $row["Add_Date"] . "', '" . $row["Description"] . "', '" . $row["Wrd/Phr"] . "');";
            fwrite($file, $sqlData);
        }

        fclose($file);
?>
        <html>

        <head>
            <title>Password Details Export</title>
        </head>

        <body>
            <h1>Details Export </h1>
            <p>We understand that you may no longer be interested in using our product, or may just want to perform a regular local backup of your details.</p>
            <p>Do not worry, we offer an "easy" way of doing so!</p>
            <ol>
                <li>
                    Enter a password below, which will be used to encrypt/decrypt your database.
                </li>
                <li>
                    After clicking on Export, download the encrypted database on being prompted.
                </li>
                <li>
                    Enter the following command :<br>
                    openssl enc -d -aes-256-cbc -md sha512 -pbkdf2 -iter 1000000 -salt -in "Path to encrypted file" -out "Path to new unencrypted file" -k "Password"
                </li>
                <li>
                    Voila! The new unencrypted database is the one with your details securely stored!
                </li>
            </ol>
            <form method="POST">
                <input type="text" name="db-pwd" placeholder="Enter DB Import Password" id="db-pwd" required>
                <input type="submit" name="submit" value="Import">
            </form>
        </body>

        </html>
<?php
    } else {
        echo "No results found.";
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/vault';
        header("Refresh:0.5,url= $referer");
        die();
    }

    $conn->close();
} else {
    $filename = "/var/www/html/password_maintenance/sql/User_Table.sql";
    $encryptionPassword = $_POST['db-pwd'];
    $encryptedFilename = "User_Table_encrypted.sql.enc";
    $filePath = '/var/www/html/password_maintenance/sql/' . $encryptedFilename;
    $command = "openssl enc -aes-256-cbc -md sha512 -pbkdf2 -iter 1000000 -salt -in $filename -out $filePath -k $encryptionPassword";
    shell_exec($command);
    echo "<script>alert('Data exported as SQL table and encrypted with a password to $encryptedFilename.')</script>";
    if (file_exists($filePath)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Length: ' . filesize($filePath));
        ob_clean();
        flush();
        readfile($filePath);
        exit();
    }
    header("Refresh:0.5,url=/vault");
    die();
}
