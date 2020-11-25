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
    $pnr = BlockSQLInjection($_GET['pnr']);
    $ticket_table = "getTicket($pnr)";
    $passengers = fetchAll($db, $ticket_table);
    $query = "select getTrainDetails($pnr)";
    $result = pg_query($db, $query);
    if($result){
        $row = pg_fetch_all($result);
        $row = $row[0]['gettraindetails'];
        list($first, $second) = explode(",", $row);
        $train_id = substr($first, 1); 
        $date = substr($second, 0, -1);
        // print_r($row);
    }
    
    
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
        <?php require_once 'nav.php'; ?>
        <div id="content">
        <?php endif; ?>
        <?php if(isset($_GET['pnr'])): ?>
        <h2>Ticket for PNR <?php echo $_GET['pnr']; ?></h2>
        <center>
        <h3>Booking for Train #<b><?php echo sprintf("%06d",$train_id); ?></b> on <b><?php echo $date; ?></b></h3>
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
            if($passengers):
                foreach($passengers as $passenger): ?>
                <tr>
                    <td><?php echo $passenger['name']; ?></td>
                    <td><?php echo $passenger['age']; ?></td>
                    <td><?php echo $passenger['gender']; ?></td>
                    <td><?php echo $passenger['coach']; ?></td>
                    <td><?php echo $passenger['berth']; ?> (<?php echo $passenger['berth_type']; ?>)</td>
                </tr>
                <?php endforeach;

                else: ?>
                    <tr>
                        <td colspan="5">Couldn't find your ticket!</td>
                    </tr>
        <?php endif; ?>
        </table>
        <a href="checkpnr.php">Check Another PNR</a>
        </center>
        <?php else: ?>
            <form action="" method="get">
            <input type="text" id="username" name="pnr" placeholder="Enter PNR" style="margin-top:50vh; transform: translate(-50%,-50%);">
            <center>
                <input type="submit" value="CHECK"><a class="btn" href="agent.php">CANCEL</a>
        
            </center>
            </form>
        <?php endif; ?>
            
        </div>
    </body>
</html>
