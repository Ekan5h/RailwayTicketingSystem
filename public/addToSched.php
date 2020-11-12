<?php

require_once 'config.php';
require_once 'dbFunctions.php';

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
        <title>Add to Schedule</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <h2>Add to Schedule:</h2>
        <h3>For train <?php echo $_GET['name']; ?>, ID = <?php echo $_GET['train_id']; ?></h3>
        <form method="post" action=''>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date">
            <label for="num_ac">AC coaches:</label>
            <input type="number" id="num_ac" name="num_ac" min="1" max="999">
            <label for="num_sl">SL coaches:</label>
            <input type="number" id="num_sl" name="num_sl" min="1" max="999">
            <input type="submit" name="submit" value="Schedule">

        </form>
        <?php
            if(isset($_GET['success'])){
                if($_GET['success'] == "1"){
                    echo "Train inserted in schedule!\n";
                }
                else{
                    echo "An error occurred!\n";
                }
            }
        ?>
        <a href="trains.php">Back to list of trains</a>
    </body>
</html>