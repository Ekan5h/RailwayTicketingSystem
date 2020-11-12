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

if (!empty($_POST)) {
    if(isset($_POST['book2'])){
        $booking_agent = $_GET['booking_agent'];
        $train_id = $_GET['train_id'];
        $date = $_GET['date'];
        $num_seats = $_GET['num_seats'];
        $coach = $_GET['coach'];
        $query = "select allotK($train_id, '$date', $num_seats, '$coach', $booking_agent, ";
        $names = "ARRAY[";
        $ages = "ARRAY[";
        $genders = "ARRAY[";
        for($j = 1; $j <= intval($num_seats); $j++) {
            $names .= "'".$_POST['name'.strval($j)]."'";
            $ages .= $_POST['age'.strval($j)];
            $genders .= "'".$_POST['gender'.strval($j)]."'";
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
        $pnr = pg_query($db, $query);
        if ($pnr) {
            // show ticket link
        } else {
            $loc = "Location: failure.php";
        }
        header($loc);
    }
    else{
        $booking_agent = $_GET['booking_agent'];
        $train_id = $_GET['train_id'];
        $date = $_GET['date'];
        $num_seats = $_POST['num_seats'];
        $coach = $_POST['coach'];
        $loc = "Location: bookTicket.php?booking_agent=$booking_agent&train_id=$train_id&date=$date&num_seats=$num_seats&coach=$coach";
        header($loc);
    }
}


?>

<!DOCTYPE html>
    <head>
        <title>Book ticket</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <h2>Book a ticket</h2>
        <h3>Booking agent <?php echo $_GET['booking_agent']; ?></h3>
        <h3>For train ID = <?php echo $_GET['train_id']; ?>, date =  <?php echo $_GET['date']; ?></h3>
        <?php 
            if(!isset($_GET['num_seats'])){ ?>
                <form method="post" action=''>
                    <label for="num_seats">Number of seats:</label>
                    <input type="number" id="num_seats" name="num_seats" min="1" max="999">
                    <label for="coach">Coach:</label>
                    <select name="coach" id="coach">
                        <option value="ac">AC</option>
                        <option value="sl">SL</option>
                    </select>
                    <input type="submit" name="book1" value="Book">
                </form>
        <?php }  else { ?>
            
            <h3>Number of seats = <?php echo $_GET['num_seats']; ?>, coach =  <?php echo $_GET['coach']; ?></h3>
            <form method="post" action=''>
            <?php
                for ($i = 1; $i <= intval($_GET['num_seats']); $i++) { ?>
                    
                    <label for="name<?php echo $i; ?>">Name:</label>
                    <input type="text" name="name<?php echo $i; ?>" id="name<?php echo $i; ?>" required>
                    <label for="age<?php echo $i; ?>">Age</label>
                    <input type="number" id="age<?php echo $i; ?>" name="age<?php echo $i; ?>" min="1" max="99">
                    <label for="gender<?php echo $i; ?>">Gender</label>
                    <select name="gender<?php echo $i; ?>" id="gender<?php echo $i; ?>">
                        <option value="m">Male</option>
                        <option value="f">Female</option>
                        <option value="n">Non-binary</option>
                    </select>
                    <br>
            
                <?php } ?>
                <input type="submit" name="book2" value="Book">
            </form>
        <?php } ?>


    </body>
</html>