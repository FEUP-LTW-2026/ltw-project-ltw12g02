<?php

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/users.class.php');

function generateHead(string $title) {
    echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $title . '</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="../js/messages.js" defer></script>
    <script src="../js/classes.js" defer></script>
    <script src="../js/popup.js" defer></script>
    <script src="../js/profile.js" defer></script>
    <script src="../js/booking_class.js" defer></script>
</head>
<body>';
}

function generateHeader(Session $session) { ?>
    <header class="site-header">
        <input type="checkbox" id="menu-check">
        <label for="menu-check" id="menu-icon">&#9776;</label>

        <a href="index.php">
            <img src="../html/PowerPIT.png" alt="logo" width="50" height="50">
        </a>

        <nav class="site-nav">
            <div id="profile">
                <img src="../html/PowerPIT.png" alt="logo" width="100" height="50">
            </div>
            <ul>
                <li><a href="index.php#aboutus"><i class="fa fa-id-card" aria-hidden="true"></i> About&nbsp;us</a></li>
                <li><a href="classes.php"><i class="fa fa-users" aria-hidden="true"></i> Classes</a></li>
                <li><a href="trainers.php"><i class="fa fa-id-badge" aria-hidden="true"></i> Trainers</a></li>
                <li><a href="equipment.php"><i class="fa fa-th" aria-hidden="true"></i> Equipment</a></li>
                <?php if ($session->getRole() === 'admin'){ ?>
                <li><a href="admin.php"><i class="fa fa-shield" aria-hidden="true"></i> Admin</a></li>
                <?php } ?>
            </ul>
        </nav>

        <div id="signup">
            <?php 
            if ($session->isLoggedIn()) { 
                $db = getDatabaseConnection();
                $user = Users::getUser($db, $session->getId());

                if ($user !== null) { ?>
                    <input type="checkbox" id="profile-check">

                    <label for="profile-check" id="profile">
                        <img 
                            src="../assets/users/<?= htmlspecialchars($user->getProfileImage()) ?>" 
                            alt="Profile picture" 
                            width="50" 
                            height="50"
                        >  
                    </label>

                    <nav class="profile-nav">
                        <div id="profile">
                            <img 
                                src="../assets/users/<?= htmlspecialchars($user->getProfileImage()) ?>" 
                                alt="Profile picture" 
                                width="35" 
                                height="35"
                            >

                            <?= htmlspecialchars($user->getUserName()) ?>
                        </div>

                        <ul>
                            <li><a href="profile.php"><i class="fa fa-user-circle" aria-hidden="true"></i> Profile</a></li>
                            <li><a href="../actions/action_logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a></li>
                        </ul>
                    </nav>
                <?php } else { ?>
                    <a href="register.php" class="btn small">Register</a>
                    <a href="login.php" class="btn small light">Login</a>
                <?php } ?>

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
