<?php
    session_start();
    if (!isset($_SESSION['User_ID'])) {
        header("Location: /authentication");
        die();
    }

    $userId = $_SESSION['User_ID']; 
    $website = $_SESSION['Website']; 
    
    $db_host = "db";
    $db_username = "root";
    $db_password = "MYSQL_ROOT_PASSWORD";
    $db_name = "PM_1";
    
    $conn = new mysqli($db_host, $db_username, $db_password, $db_name);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $sql = "SELECT * FROM User_Info WHERE User_ID = $userId AND Website = $website";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        while($row=$result->fetch_assoc()) {
            // $row = $result->fetch_assoc();
            // echo '<script>';
            // echo 'document.getElementById("websiteField").value = "' . $row["Website"] . '";';
            // echo 'document.getElementById("linkField").value = "' . $row["Link"] . '";';
            // echo 'document.getElementById("usernameField").value = "' . $row["Username"] . '";';
            // echo 'document.getElementById("passwordField").value = "' . $row["Password"] . '";';
            // echo 'document.getElementById("descriptionField").value = "' . $row["Description"] . '";';
            // echo '</script>';
            echo json_encode($row);
        }
    } else {
        echo json_encode(['error' => 'Password not found or unauthorized access']);
        // echo '<p>Password not found or unauthorized access.</p>';
    }
    $conn->close();
?>