<?php
session_start();

require_once 'config.php';
require_once 'dbFunctions.php';

?>
<!DOCTYPE html>
    <head>
        <title>Failure</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
        <link rel = "stylesheet" href = "css/main.css">
    </head>
    <body>
        <img src="img/trainO.png" id="train" style="transform: scaleX(-1); left: auto; right: -5vh;">
        <?php if(!empty($_SESSION['email'])): ?>
        <?php require_once 'nav.php'; ?>
        <div id="content">
        <?php endif; ?>
        <center>
        <h2 style="margin-top: 50vh; transform:translate(0%, -50%);">Couldn't find enough seats!</h2>
        <a href="agent.php">Go back to booking page.</a>
        </center>
        </div>
    </body>
</html>