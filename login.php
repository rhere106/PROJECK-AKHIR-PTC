<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENSI</title>
    <link rel="icon" type="image/x-icon" href="css/img/sensi.png">
    <link rel="stylesheet" href="css/styleLoginn.css">
</head>
<body>
    <?php
    require 'header1.php';
    ?>
    <main>
        <div class="container">
            <img src="css/img/sensi.png" alt="">
            <h3>Selamat Datang</h3>
            <form action="login_handler.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Masuk</button>
            </form>
            <?php
            if (isset($_GET['error'])) {
                echo "<p style='color: red;'>" . htmlspecialchars($_GET['error']) . "</p>";
            }
            ?>
        </div>
    </main>
</body>
</html>
