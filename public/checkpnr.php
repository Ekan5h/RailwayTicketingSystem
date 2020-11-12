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
$pnr = $_GET['pnr'];
$ticket_table = "ticket_$pnr";
$passengers = fetchAll($db, $ticket_table);

?>

<!DOCTYPE html>
    <head>
        <title>Check PNR</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <h2>Ticket for PNR <?php echo $_GET['pnr']; ?></h2>
        <table>
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
            <?php endif ?>
        </table>
        
    </body>
</html>