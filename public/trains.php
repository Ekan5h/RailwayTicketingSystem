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
$trains = fetchAll($db, 'trains');

if (!empty($_POST)) {
    $name = $_POST['name'];
    $query = "Insert into trains(name, created_on) values('$name', now())";
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
        <title>Railway Ticketing System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
        <link rel = "stylesheet" href = "css/main.css">
    </head>
    <body>
        <img src="img/trainO.png" id="train">
        <center>
        <h2>Schedule a TrainA</h2>
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
        <table cellspacing=0>
            <thead>
                <tr>
                    <th>Train ID</th>
                    <th>Name</th>
                    <th></th>
                </tr>
            </thead>
            <?php
            if($trains):
                foreach($trains as $train): ?>
                <tr>
                    <td><?php echo sprintf("%06d",$train['train_id']); ?></td>
                    <td><?php echo $train['name']; ?></td>
                    <td><a class='btn' href="addToSched.php?train_id=<?php echo $train['train_id']; ?>&name=<?php echo $train['name']; ?>">Add to schedule</a></td>
                </tr>
                <?php endforeach;

                else: ?>
                    <tr>
                        <td colspan="2">No trains listed!</td>
                    </tr>
            <?php endif ?>
        </table>
        </center>
        <form method="post" action='?'>
            <input type="text" name="name" id="name" placeholder="New Train Name" required>
            <br>
            <center>
            <input type="submit" name="submit" value="Insert New Train">
            
        </form>
        <a class="btn" href="trainsched.php">Back</a>

    </body>
</html>