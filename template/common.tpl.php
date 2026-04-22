<?php 
    function generateHead() {
       return ' <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../html/style.css">
    <title>Gym</title>
</head>';
    }
    function generateHeader() { 

    return  '<header class="site-header">
        <label for="menu-check" id="menu-icon">
        &#9776;
        </label>
        <a href="index.html"><img src="https://fastly.picsum.photos/id/180/50/50.jpg?hmac=XMW9Xfp3fH-OoI44sBBOzJib8r8soPYbeUHO0d-OT20" alt="logo" width="50" height="50"></a>
        <input type="checkbox" id="menu-check">
        <nav id="pages">
            <ul>
                <li><a href="index.html">About us</a></li>
                <li><a href="index.html">Classes</a></li>
                <li><a href="index.html">Trainers</a></li>
                <li><a href="index.html">Equipment</a></li>
            </ul>
        </nav>
        <div id="signup">
            <a href="register.html" class="small-button">Register</a>
            <a href="login.html" class="small-button-light">Login</a>
        </div>
    </header>';


    }

    function generateFooter() { 

    return '<footer class="main-footer">
        <p>Copyright &copy; All right reserved</p>
        <p>Rua njdfbajlfhak 00, 4000-000 Cidade | email@powerpit.com | +555 900 000 000</p>
        <p>Our transactions are made in euros (&euro;).</p>
    </footer>';

    }

