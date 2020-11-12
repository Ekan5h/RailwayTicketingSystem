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

if(isset($_GET['pnr'])){
    $pnr = $_GET['pnr'];
    $ticket_table = "ticket_$pnr";
    $passengers = fetchAll($db, $ticket_table);
}
?>

<!DOCTYPE html>
    <head>
        <title>Check PNR</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
        <link rel = "stylesheet" href = "css/main.css">
    </head>
    <body>
        <img src="img/trainO.png" id="train" style="transform: scaleX(-1); left: auto; right: -5vh;">
        <?php if(!empty($_SESSION['email'])): ?>
        <nav>
            <div id="profile"></div>
            <center>Hi, <?php echo $_SESSION['name'] ?>!</center><br>
            <ul>
                <li><a href="#">PAST TICKETS</a></li>
                <li><a href="checkpnr.php">CHECK PNR</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </nav>
        <div id="content">
        <?php endif; ?>
        <?php if(isset($_GET['pnr'])): ?>
        <h2>Ticket for PNR <?php echo $_GET['pnr']; ?></h2>
        <center>
        <table cellspacing="0px">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Coach</th>
                    <th>Berth</th>
                </tr>
            </thead>
            <?php
            if(count($passengers) > 0):
                foreach($passengers as $passenger): ?>
                <tr>
                    <td><?php echo $passenger['name']; ?></td>
                    <td><?php echo $passenger['age']; ?></td>
                    <td><?php echo $passenger['gender']; ?></td>
                    <td><?php echo $passenger['coach']; ?></td>
                    <td><?php echo $passenger['berth']; ?></td>
                </tr>
                <?php endforeach;

                else: ?>
                    <tr>
                        <td colspan="5">Couldn't find your ticket!</td>
                    </tr>
        <?php endif; ?>
        </table>
        <a href="checkpnr.php">Cancel</a>
        </center>
        <?php else: ?>
            <form action="" method="get">
            <input type="text" id="username" name="pnr" placeholder="Enter PNR" style="margin-top:50vh; transform: translate(-50%,-50%);">
            <center>
                <input type="submit" value="CHECK"><a href="agent.php">Cancel</a>
        
            </center>
            </form>
        <?php endif; ?>
            
        </div>
    </body>
</html>