<?php
require_once('./config/db.php');
$query = "select * from User_Info";
$result = mysqli_query($con,$query);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stlesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Vault</title>
</head>
<body>
    <div class="sidebar">
        <div class="logo_content">
            <div class="logo">
                <i class='bx bxl-netlify'></i>
                <div class="logo_name">Password Manager</div>
            </div>
        <i class="bx bx-menu" id="btn"></i>
        <ul class="nav_list">
            <li>
                <i class='bx bx-search' ></i>
                <input type="text" placeholder="Search...">
                <span class="tooltip">Search</span>
            </li>
            <li>
                <a href="#">
                    <i class='bx bx-grid-alt'></i>
                    <span class="links_name">Dashboard</span>
                </a>
                <span class="tooltip">Dashboard</span>
            </li>
            <li>
                <a href="#">
                    <i class='bx bx-user'></i>
                    <span class="links_name">User</span>
                </a>
                <span class="tooltip">User</span>
            </li>
            <li>
                <a href="#">
                    <i class='bx bx-chat'></i>
                    <span class="links_name">Messages</span>
                </a>
                <span class="tooltip">Messages</span>
            </li>
            <!-- <li>
                <a href="#">
                    <i class='bx bx-folder'></i>
                    <span class="links_name">File Manager</span>
                </a>
                <span class="tooltip">Files</span>
            </li> -->
            <li>
                <a href="#">
                    <i class='bx bx-heart'></i>
                    <span class="links_name">Saved</span>
                </a>
                <span class="tooltip">Saved</span>
            </li>
            <li>
                <a href="#">
                    <i class='bx bx-cog'></i>
                    <span class="links_name">Settings</span>
                </a>
                <span class="tooltip">Settings</span>
            </li>
        </ul>
        <div class="profile_content">
            <div class="profile">
                <div class="profile_details">
                    <img src="profile.jpg" alt="">
                    <div class="name_job">
                        <div class="name">Jeevesh</div>
                        <div class="job">Designer</div>
                    </div>
                </div>
                <i class="bx bx-log-out" id="log_out"></i>
            </div>
        </div>
    </div>
    <div class="home_content">
        <div class="text">Dashboard</div>
        <p> hi</p>
        <div class="card-body">
            <table class="table table-bordered text-center">
                <tr class="bg-dark text-white">
                    <td> Description </td>
                    <td> Link </td>
                    <td> Password </td>
                    <td> Edit </td>
                    <td> Delete </td>
                </tr>
                <tr>
                <?php
                    while($row = mysqli_fetch_assoc($result)) {
                ?>  

                    <td><?php echo $row['Description'];?></td>
                    <td><?php echo $row['Link'];?></td>
                    <td><?php echo $row['Password'];?></td> 
                    <td><a href="#" class="btn btn-primary">Edit</a></td>
                    <td><a href="#" class="btn btn-danger">Delete</a></td>      

                </tr>
                <?php
                    } 
                ?>
                
            </table>
        </div>
    </div>    
</body>

<script>
    let btn = document.querySelector("#btn");
    let sidebar = document.querySelector(".sidebar");
    let searchBtn = document.querySelector(".bx-search");
    
    btn.onclick = function () {
        sidebar.classList.toggle("active");
    }
    searchBtn = function () {
        sidebar.classList.toggle("active");
    }
    ;
</script>

</html>