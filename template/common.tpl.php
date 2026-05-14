<?php

function generateHead(string $title) {
    echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $title . '</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/messages.js" defer></script>
    <script src="../js/classes.js" defer></script>
</head>
<body>';
}

function generateHeader(Session $session) { ?>
    <header class="site-header">
        <input type="checkbox" id="menu-check">
        <label for="menu-check" id="icon menu-icon">&#9776;</label>

        <a href="index.php">
            <img src="../html/PowerPIT.png" alt="logo" width="50" height="50">
        </a>

        <nav class="site-nav">
            <ul>
                <li><a href="index.php#aboutus" class="header-text">About&nbsp;us</a></li>
                <li><a href="classes.php" class="header-text">Classes</a></li>
                <li><a href="trainers.php" class="header-text">Trainers</a></li>
                <li><a href="equipment.php" class="header-text">Equipment</a></li>
            </ul>
        </nav>

        <div id="signup">
            <?php if ($session->isLoggedIn()) { ?>
                <a href="profile.php" class="header-text">
                    username
                </a>
                <a href="../actions/action_logout.php" class="icon">D</a>
            <?php } else { ?>
                <a href="register.php" class="btn small">Register</a>
                <a href="login.php" class="btn small light">Login</a>
            <?php } ?>
        </div>
    </header>
<?php }

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



function drawMessages(array $messages): void { ?>
    <?php if (!empty($messages)) { ?>
        <div class="message_area">
            <?php foreach ($messages as $message) { ?>
                <div class="message <?= htmlspecialchars($message['type']) ?>">
                    <span><?= htmlspecialchars($message['text']) ?></span>

                    <button type="button" class="message_close" aria-label="Close message">
                        &times;
                    </button>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
<?php } ?>
