<?php
session_start();
require_once 'config.php';
require_once 'dbFunctions.php';
if(empty($_SESSION['admin'])){
    header("Location: admlogin.php", TRUE, 301);
    exit();
}
$db = connect(
    DB_HOST,
    DB_NAME,
    DB_PORT,
    DB_USERNAME,
    DB_PASSWORD
);
$query = "SELECT *, empty_seats(train_id, date) as empty_seats, total_seats(train_id, date) as total_seats FROM train_sched NATURAL JOIN trains";
$result = pg_query($db, $query);
$trains = pg_fetch_all($result);
?>

<!DOCTYPE html>
    <head>
        <title>Railway Ticketing System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
        <link rel = "stylesheet" href = "css/main.css">
    </head>
    <body>
        <img src="img/trainO.png" id="train">
        <center>
        <h2>Train Schedule</h2>
        <table cellspacing=0>
            <thead>
                <tr>
                    <th>Train ID</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>AC Coaches</th>
                    <th>SL Coaches</th>
                    <th>Availability</th>
                </tr>
            </thead>
            <?php
            if($trains):
                foreach($trains as $train): ?>
                <tr>
                    <td><?php echo sprintf("%06d",$train['train_id']); ?></td>
                    <td><?php echo $train['name']; ?></td>
                    <td><?php echo $train['date']; ?></td>
                    <td><?php echo $train['num_ac']; ?></td>
                    <td><?php echo $train['num_sl']; ?></td>
                    <td><?php echo $train['empty_seats']; ?> / <?php echo $train['total_seats']; ?></td>
                </tr>
                <?php endforeach;

                else: ?>
                    <tr>
                        <td colspan="6">No trains listed!</td>
                    </tr>
            <?php endif ?>
        </table>
        <a class="btn" href="trains.php">Schedule a new train</a><br><br><br>
        <a class="btn" href="logout.php">Logout</a>
        </center>
    </body>
</html>