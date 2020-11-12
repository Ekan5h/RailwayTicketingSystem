<?php

require_once 'config.php';
require_once 'dbFunctions.php';

$db = connect(
    DB_HOST,
    DB_NAME,
    DB_PORT,
    DB_USERNAME,
    DB_PASSWORD
);

$query = "SELECT name, train_id, date, empty_seats(train_id, date) as empty_seats, total_seats(train_id, date) as total_seats FROM train_sched NATURAL JOIN trains WHERE date > now()::date;";
$result = pg_query($db, $query);
$trains = pg_fetch_all($result);
?>
<html>
    <head>
        <title>Railway Ticketing System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
        <link rel = "stylesheet" href = "css/main.css">
    </head>
    <body>
        <img src="img/trainO.png" id="train" style="transform: scaleX(-1); left: auto; right: -5vh;">
        <nav>
            <div id="profile"></div>
            <ul>
                <li><a href="#">PAST TICKETS</a></li>
                <li><a href="#">CHECK PNR</a></li>
                <li><a href="#">LOGOUT</a></li>
            </ul>
        </nav>
        <div id="content">
            <center>
                <input type="text" id="searchbar" placeholder="Type in train id or name"><div id="search" onclick="document.getElementById('searchbtn').click()"><input id="searchbtn" type="submit"></div>
            </center>
            <?php
            if(count($trains) > 0):
                foreach($trains as $train): ?>
                <div class="record">
                    <div class="train-name"><?php echo $train['name']; ?></div>
                    <div class="train-id"><?php echo sprintf('%06d', $train['train_id']); ?></div>
                    <div class="date"><?php echo $train['date']; ?></div>
                    <div class="booked">Available: <?php echo $train['empty_seats']; ?> / <?php echo $train['total_seats']; ?></div>
                    <div class="book">BOOK</div>
                </div>
                <?php endforeach;

                else: ?>
                <div class="record">
                    <center> No scheduled trains at the moment.</center>
                </div>
            <?php endif ?>
            <div class="record"></div>
        </div>
    </body>
</html>