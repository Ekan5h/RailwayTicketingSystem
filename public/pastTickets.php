<?php
session_start();
require_once 'config.php';
require_once 'dbFunctions.php';

if(empty($_SESSION['email'])){
    header("Location: index.php", TRUE, 301);
    exit();
}

$db = connect(
    DB_HOST,
    DB_NAME,
    DB_PORT,
    DB_USERNAME,
    DB_PASSWORD
);

$booking_agent = $_SESSION['email'];
$booking_agent = str_replace("@", "_", $booking_agent);
$booking_agent = str_replace(".", "_", $booking_agent);
$table_name = "past_bookings_$booking_agent";

$query = "SELECT pnr, to_char(getTrainID(pnr), '000000') train_id, getTrainDate(pnr) date, created_on::date FROM $table_name";
$result = pg_query($db, $query);
$tickets = pg_fetch_all($result);
?>

<!DOCTYPE html>
    <head>
        <title>Past Tickets</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">      
        <link rel = "stylesheet" href = "css/main.css">                                                           
    </head>
    <body>
        <img src="img/trainO.png" id="train" style="transform: scaleX(-1); left: auto; right: -5vh;">
        <?php require_once 'nav.php'; ?>
        <div id="content">
        <h2>Past tickets</h2>
        <center>
        <table cellspacing=0>
            <?php
            if($tickets): ?>
            <thead>
                <tr>
                    <th>PNR</th>
                    <th>Train ID</th>
                    <th>Journey Date</th>
                    <th>Booked On</th>
                </tr>
            </thead>
            <?php
                foreach($tickets as $ticket): ?>
                <tr>
                    <td><a href="checkpnr.php?pnr=<?php echo $ticket['pnr']; ?>"><?php echo $ticket['pnr']; ?></a></td>
                    <td><?php echo $ticket['train_id']; ?></td>
                    <td><?php echo $ticket['date']; ?></td>
                    <td><?php echo $ticket['created_on']; ?></td>
                
                </tr>
                <?php endforeach;

                else: ?>
                    <tr>
                        <td colspan="2">No tickets found!</td>
                    </tr>
            <?php endif ?>
        </table>
        </center>
    </body>
</html>