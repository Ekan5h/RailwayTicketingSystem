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
// echo "TEST2\n";
if (!empty($_POST)) {
    if(isset($_POST['book2'])){
        $booking_agent = $_SESSION['email'];
        $train_id = BlockSQLInjection($_GET['train_id']);
        $date = BlockSQLInjection($_GET['date']);
        $num_seats = BlockSQLInjection($_GET['num_seats']);
        $coach = BlockSQLInjection(strtoupper($_GET['coach']));
        $query = "select allotK($train_id, '$date', $num_seats, '$coach', '$booking_agent', ";
        $names = "ARRAY[";
        $ages = "ARRAY[";
        $genders = "ARRAY[";
        for($j = 1; $j <= intval($num_seats); $j++) {
            $names .= "'".BlockSQLInjection($_POST['name'.strval($j)])."'";
            $ages .= BlockSQLInjection($_POST['age'.strval($j)]);
            $genders .= "'".BlockSQLInjection($_POST['gender'.strval($j)])."'";
            if($j == intval($num_seats)){
                $names .= "]";
                $ages .= "]";
                $genders .= "]";
            } else {
                $names .= ",";
                $ages .= ",";
                $genders .= ",";
            }
        }
        $query .= $names.",".$ages.",".$genders.")";
        // echo $query."\n";
        $result = pg_query($db, $query);
        // echo $result."\n";
        // exit();
        if ($result) {
            $pnr = pg_fetch_result($result, 0, 0);
            // print_r($pnr);
            $loc = "Location: checkpnr.php?pnr=$pnr";
        } else {
            $loc = "Location: failure.php";
        }
        // echo $loc."\n";
        // echo $pnr."\n";
        header($loc);
    }
    else{
        $booking_agent = $_SESSION['email'];
        $train_id = $_GET['train_id'];
        $date = $_GET['date'];
        $num_seats = $_POST['num_seats'];
        $coach = $_POST['coach'];
        $loc = "Location: bookTicket.php?train_id=$train_id&date=$date&num_seats=$num_seats&coach=$coach";
        header($loc);
    }
}


?>

<!DOCTYPE html>
    <head>
        <title>Book a ticket</title>
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
        <h2>Book a ticket</h2>
        <h3>Booking by <b><?php echo $_SESSION['email']; ?></b> for train #<b><?php echo sprintf("%06d",$_GET['train_id']); ?></b> on  <b><?php echo $_GET['date']; ?></b></h3>
        <center>
        <?php 
            if(!isset($_GET['num_seats'])){ ?>
                <form method="post" action=''>
                    <label for="num_seats">Number of seats:</label>
                    <input type="number" id="num_seats" name="num_seats" min="1" max="999">
                    <label for="coach">Coach:</label>
                    <select name="coach" id="coach">
                        <option value="AC">AC</option>
                        <option value="SL">SL</option>
                    </select>
                    <input type="submit" name="book1" value="Continue"><a href="agent.php">Cancel</a>
                </form>
        <?php }  else { ?>
            
            <h3><?php echo $_GET['num_seats']; ?> berths in <?php echo $_GET['coach']; ?> class coming right up...</h3>
            <form method="post" action=''>
            <?php
                for ($i = 1; $i <= intval($_GET['num_seats']); $i++) { ?>
                    
                    <label for="name<?php echo $i; ?>">Name:</label>
                    <input type="text" name="name<?php echo $i; ?>" id="name<?php echo $i; ?>" required>
                    <label for="age<?php echo $i; ?>">Age</label>
                    <input type="number" id="age<?php echo $i; ?>" name="age<?php echo $i; ?>" min="1" max="99" required>
                    <label for="gender<?php echo $i; ?>">Gender</label>
                    <select name="gender<?php echo $i; ?>" id="gender<?php echo $i; ?>">
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                        <option value="N">Non-binary</option>
                    </select>
                    <br>
            
                <?php } ?>
                <br>
                <input type="submit" name="book2" value="Book"><a href="agent.php">Cancel</a>
            </form>
        <?php } ?>
        </center>
        <div>
        
    </body>
</html>