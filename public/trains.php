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
// print_r($trains);
// echo 'CHECK3';
// foreach($trains as $train):
//     echo $train['name'];
// endforeach;
?>

<!DOCTYPE html>
    <head>
        <title>Trains</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <h2>All trains</h2>
        <table>
            <thead>
                <tr>
                    <th>Train ID</th>
                    <th>Name</th>
                </tr>
            </thead>
            <?php
            if(count($trains) > 0):
                foreach($trains as $train): ?>
                <tr>
                    <td><?php echo $train['train_id']; ?></td>
                    <td><?php echo $train['name']; ?></td>
                </tr>
                <?php endforeach;

                else: ?>
                    <tr>
                        <td colspan="2">No trains listed!</td>
                    </tr>
            <?php endif ?>
        </table>
    </body>
</html>