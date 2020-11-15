<?php
session_start();
require_once 'config.php';
require_once 'dbFunctions.php';
if(empty($_SESSION['admin'])){
    header("Location: admlogin.php", TRUE, 301);
    exit();
}
$db = connect(DB_HOST, DB_NAME, DB_PORT, DB_USERNAME, DB_PASSWORD);
if (!empty($_POST)) {
    $name = $_GET['name'];
    $train_id = $_GET['train_id'];
    $date = $_POST['date'];
    $num_ac = $_POST['num_ac'];
    $num_sl = $_POST['num_sl'];
    $query = "Insert into train_sched values($train_id, '$date', $num_ac, $num_sl)";
    echo $query;
    $res = pg_query($db, $query);
    if ($res) {
        $success = "1";
    } else {
        $success = "0";
    }
    $loc = "Location: addToSched.php?success=$success&train_id=$train_id&name=$name";
    header($loc);
}

?>

<!DOCTYPE html>
    <head>
        <title>Railway Ticketing System</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
        <link rel = "stylesheet" href = "css/main.css">
    </head>
    <body>
        <img src="img/trainO.png" id="train">
        <center>
        <h2>Add Train to Schedule</h2>
        <h3>Schedule train <b><?php echo $_GET['name']; ?></b>, ID = <b><?php echo sprintf("%06d",$_GET['train_id']); ?></b></h3>
        <form method="post" action=''>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date">
            <label for="num_ac">AC coaches:</label>
            <input type="number" id="num_ac" name="num_ac" min="1" max="999">
            <label for="num_sl">SL coaches:</label>
            <input type="number" id="num_sl" name="num_sl" min="1" max="999">
            <br><br>
            <input type="submit" name="submit" value="Schedule">

        </form>
        <?php
            if(isset($_GET['success'])){
                if($_GET['success'] == "1"){
                    echo "<font color=\"green\">Train inserted in schedule!</font><br><br>";
                }
                else{
                    echo "<font color=\"red\">An error occurred! Check if the train is not already scheduled.</font><br><br>";
                }
            }
        ?>
        <a class="btn" href="trains.php">Back to list of trains</a>
    </body>
</html>