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

if (!empty($_POST)) {
    $id = strval(count($trains) + 1);
    $name = $_POST['name'];
    $query = "Insert into trains values($id, '$name')";
    $res = pg_query($db, $query);
    if ($res) {
        $success = "1";
    } else {
        $success = "0";
    }
    $loc = "Location: trains.php?success=$success";
    header($loc);
}

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
                    <td><a href="addToSched.php?train_id=<?php echo $train['train_id']; ?>&name=<?php echo $train['name']; ?>">Add to schedule</a></td>
                </tr>
                <?php endforeach;

                else: ?>
                    <tr>
                        <td colspan="2">No trains listed!</td>
                    </tr>
            <?php endif ?>
        </table>
        <form method="post" action='?'>
    	    <label for="name">Train Name:</label>
            <input type="text" name="name" id="name" required>
            <input type="submit" name="submit" value="Insert">
        </form>
        <?php
            if(isset($_GET['success'])){
                if($_GET['success'] == "1"){
                    echo "Train inserted!\n";
                }
                else{
                    echo "An error occurred!\n";
                }
            }
        ?>
    </body>
</html>