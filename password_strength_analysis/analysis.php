<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
require_once(__DIR__ . '/securecheck.php');
$con = mysqli_connect("db", "root", "MYSQL_ROOT_PASSWORD", "PM_1");

if (!$con) {
    die("Connection Error");
}

$sql = "SELECT `Password`,`IV`,`Wrd/Phr` FROM `User_Info` WHERE `Website`= '" . $_POST['data'] . "' AND `User_ID`= " . $_SESSION['User_ID'];
$result = mysqli_query($con, $sql);
if (!$result) {
    echo "Error in retrieving from database!";
    die();
} else if ($result->num_rows == 1) {
    while ($row = $result->fetch_assoc()) {
        $encpwd = $row['Password'];
        $type = $row['Wrd/Phr'];
        $iv = $row['IV'];
        $key = getenv('AES_KEY');
        $password = openssl_decrypt($encpwd, "AES-256-CBC", $key, iv: $iv);
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Password Strength Analysis</title>
</head>

<body>
    <?php
    if ($type == 0) {
        echo "<p>Password: " . $password . "</p>";
        if (passwordlen($password))
            echo "<p>Password is safe in length.</p><br>";
        else
            echo "<p>Password length too less to be secure!</p><br>";
        echo "<p>Basic Password Entropy: " . pwd_entropy($password) . " bits.</p>";
        if (pwd_entropy($password) >= 60)
            echo "<p>Password safe in strength! Check out advanced strength analysis for more info!</p><br>";
        else
            echo "<p>Password is weak! Check out advanced strength analysis for more info!</p><br>";
    } else if ($type == 1) {
        echo "<p>Passphrase: " . $password . "</p>";
        if (phraselen($password))
            echo "<p>Passphrase is safe in length.</p><br>";
        else
            echo "<p>Passphrase is too small!</p><br>";
        echo "<p>Basic Passphrase Entropy: " . phr_entropy(phraselen($password)) . " bits.</p>";
        if (phr_entropy(phraselen($password)) >= 60)
            echo "<p>Passphrase safe in strength! Check out advanced strength analysis for more info!</p><br>";
        else
            echo "<p>Passphrase is weak! Check out advanced strength analysis for more info!</p><br>";
    }
    echo "<p>Do you want to perform a leak lookup analysis to see if your credentials were leaked?</p>";
    echo "<p>Continue with current email: '" . $_SESSION['email'] . "' or use a enter a different one.<p>";
    ?>
    <form action="/strength-analysis/leak" method="POST">
        <input type="email" name="email">
        <input type="submit" name="submit" value="Leak Lookup Search">
    </form>
    <br>
    <p>Perform an advanced strength analysis of your password using ZXCVBN (Premium Feature)!</p>
    <form action="/advanced-strength" method="POST">
        <input type="hidden" name="type" value=<?php echo $type ?>>
        <input type="hidden" name="password" value=<?php echo $password ?>>
        <input type="submit" name="submit" value="Advanced Strength Analysis!">
    </form>
</body>

</html>