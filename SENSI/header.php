<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    // Jika belum login, redirect ke login.php
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENSI</title>
    <link rel="stylesheet" href="css/styleHeaderr.css">
    <link
      rel="stylesheet"  
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="content">
            <a href="#"><img src="css/img/ithlogo.png" alt=""></a>
            <h4>INSTITUT TEKNOLOGI BACHARUDDIN JUSUF HABIBIE</h4>
        </div>
        <div class="profile">
            <div class="profile-text">
                <span class="fullname"><?php echo htmlspecialchars($username); ?></span>
                <span class="role"><?php echo htmlspecialchars($role); ?></span>
            </div>
            <div class="profile-img">
                <img src="css/img/blank-profile-picture-973460_960_720.webp" alt="">
                <i class="fa-solid fa-bars" id="hamburgerMenu"></i>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a class="" href="editProfil.php"><i class="fa-regular fa-user"></i>   Edit Profile </a>
                    <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i>   Keluar</a>
                </div>
            </div>
        </div>
    </nav>
</body>

<script src="scriptHeaderr.js"></script>
</html>
