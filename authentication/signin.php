<?php

$conn = mysqli_connect('localhost', 'root', '', 'PM_1');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];
$sql = "SELECT `Salt`,`Password` FROM `Credentials` WHERE `Username`='$username'";
$result = mysqli_query($conn, $sql);

if (!$result)
    echo "Connection failed";
else if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hasheddata = hash('sha512', $password . $row["Salt"]);
        if ($row["Password"] == $hasheddata)
            echo "User signed in!";
    }
} else {
    echo "No user! Register now";
}

$conn->close();

?>