<?php
session_start();    
require_once 'config.php';
require_once 'dbFunctions.php';

$db = connect(
    DB_HOST,
    DB_NAME,
    DB_PORT,
    DB_USERNAME,
    DB_PASSWORD
);

if(empty($_SESSION['email'])){
    header("Location: index.php", TRUE, 301);
    exit();
}

if(!empty($_GET['s'])){
    if(is_numeric($_GET['s']))
        $query = "SELECT name, train_id, date, empty_seats(train_id, date) as empty_seats, total_seats(train_id, date) as total_seats FROM train_sched NATURAL JOIN trains WHERE date > now()::date AND train_id=".$_GET['s']." limit 10;";
    else
        $_GET['s'] = BlockSQLInjection($_GET['s']);
        $query = "SELECT name, train_id, date, empty_seats(train_id, date) as empty_seats, total_seats(train_id, date) as total_seats FROM train_sched NATURAL JOIN trains WHERE date > now()::date AND lower(name) Like '%".strtolower($_GET['s'])."%' limit 10;";
}else{
    $query = "SELECT name, train_id, date, empty_seats(train_id, date) as empty_seats, total_seats(train_id, date) as total_seats FROM train_sched NATURAL JOIN trains WHERE date > now()::date limit 10;";
}
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
        <?php require_once 'nav.php'; ?>
        <div id="content">
            <center>
                <form action="agent.php" method="get">
                    <input type="text" name="s" id="searchbar" placeholder="Type in train id or name" value="<?php if(!empty($_GET['s'])) echo $_GET['s']; ?>"><div id="search" onclick="document.getElementById('searchbtn').click()"><input id="searchbtn" type="submit"></div>
                </form>
            </center>
            <?php
            if($trains):
                foreach($trains as $train): ?>
                <div class="record">
                    <div class="train-name"><?php echo $train['name']; ?></div>
                    <div class="train-id"><?php echo sprintf('%06d', $train['train_id']); ?></div>
                    <div class="date"><?php echo $train['date']; ?></div>
                    <div class="booked">Available: <?php echo $train['empty_seats']; ?> / <?php echo $train['total_seats']; ?></div>
                    <div class="book" onclick="window.location.href = 'bookTicket.php?train_id=<?php echo $train['train_id']; ?>&date=<?php echo $train['date']; ?>';">BOOK</div>
                </div>
                <?php endforeach;

                else: ?>
                <div class="record">
                    <br>
                    <center> No scheduled trains at the moment.</center>
                </div>
            <?php endif ?>
            <div class="record"></div>
        </div>
    </body>
</html>