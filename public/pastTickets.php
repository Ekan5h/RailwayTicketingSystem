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
$trains = fetchAll($db, 'trains');

if(isset($_GET['email'])){
    $booking_agent = $_GET['email'];
    $booking_agent = str_replace("@", "_", $booking_agent);
    $booking_agent = str_replace(".", "_", $booking_agent);
    $table_name = "past_bookings_$booking_agent";
    $tickets = fetchAll($db, $table_name);
}

?>

<!DOCTYPE html>
    <head>
        <title>Past Tickets</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <h2>Past tickets</h2>
        <table>
            <thead>
                <tr>
                    <th>PNR</th>
                    <th>Created On</th>
                </tr>
            </thead>
            <?php
            if(count($tickets) > 0):
                foreach($tickets as $ticket): ?>
                <tr>
                    <td><?php echo $ticket['pnr']; ?></td>
                    <td><?php echo $ticket['created_on']; ?></td>
                </tr>
                <?php endforeach;

                else: ?>
                    <tr>
                        <td colspan="2">No tickets found!</td>
                    </tr>
            <?php endif ?>
        </table>
    </body>
</html>