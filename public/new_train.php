<?php

if (!empty($_POST)) {
    require_once 'config.php';
    require_once 'dbFunctions.php';
    $db = connect(DB_HOST, DB_NAME, DB_PORT, DB_USERNAME, DB_PASSWORD);
    echo 'Check!\n'
    // $id = strval(count($trains) + 1);
    // $name = $_POST['name'];
    // $query = 'Insert into trains values($id, '$name')';
    // $res = pg_query($db, $query);
    // if ($res) {
    //     echo "Train inserted!\n";
    // } else {
    //     echo "An error occurred!\n";
    // }
}

?>

<!DOCTYPE html>
    <head>
        <title>Trains</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <h2>Add a new train</h2>
        <form method="post" action="">
    	    <label for="name">Train Name:</label>
            <input type="text" name="name" id="name" required>
            <input type="submit" name="submit" value="Insert">
        </form>
    </body>
</html>