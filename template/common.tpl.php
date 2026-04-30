<?php

function generateHead() {
    echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PowerPIT</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>';
}

function generateHeader() {
    echo '
<header class="site-header">
    <input type="checkbox" id="menu-check">
    <label for="menu-check" id="menu-icon">&#9776;</label>

    <a href="index.php">
        <img src="../html/PowerPIT.png" alt="logo" width="50" height="50">
    </a>

    <nav class="site-nav">
        <ul>
            <li><a href="index.php">About&nbsp;us</a></li>
            <li><a href="classes.php">Classes</a></li>
            <li><a href="trainers.php">Trainers</a></li>
            <li><a href="equipment.php">Equipment</a></li>
        </ul>
    </nav>

    <div id="signup">
        <a href="register.php" class="btn small">Register</a>
        <a href="login.php" class="btn small light">Login</a>
    </div>
</header>';
}

function generateFooter() {
    echo '
<footer class="main-footer">
    <p>Copyright &copy; All rights reserved</p>
    <p>Rua njdfbajlfhak 00, 4000-000 Cidade | email@powerpit.com | +555 900 000 000</p>
    <p>Our transactions are made in euros (&euro;).</p>
</footer>

</body>
</html>';
}

?>