<?php
session_start();
require_once 'config.php';
require_once 'dbFunctions.php';

$db = connect(
    DB_HOST,
    DB_NAME,
    DB_PORT,
    DB_USERNAME,
    DB_PASSWORD
);
if(!empty($_POST)){
    try{
        $name = BlockSQLInjection($_POST['name']);
        $email = BlockSQLInjection($_POST['email']);
        $password = BlockSQLInjection($_POST['password']);
        $query = "INSERT INTO booking_agents(name, email, password) VALUES('$name', '$email', '$password')";
        $result = pg_query($db, $query);
        if(!$result){
            header("Location: signup.php?msg=Email already exists!", TRUE, 301);
            exit();
        }else{
            header("Location: index.php", TRUE, 301);
            exit();
        }
    }catch(exception $e){
        header("Location: signup.php?msg=Some error occurred!", TRUE, 301);
        exit();
    }
}
?>
<html>
    <head>
        <title>Railway Ticketing System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
        <link rel = "stylesheet" href = "css/main.css">
    </head>
    <body>
        <div id="logo">CS301</div>
        <img src="img/trainO.png" id="train">
        <center>
            <font color="red">
            <?php
                if(!empty($_GET['msg'])){
                    echo $_GET['msg'];
                    echo "<br><br>";
                }
            ?>
            </font>
        </center>
        <form action="signup.php" method="POST">
            <input type="text" name = "name" id="name" placeholder="Full Name" required>
            <input type="email" name="email" id="email" placeholder="Email ID" required>
            <input type="password" name="password" id="password" placeholder="Password" required>
            <input type="submit" id="loginbtn" value="SIGNUP">
        </form>
        <center>Already a member? <a href="index.php" style="color: orangered;">Login</a><center>

        <div id="admlgin">
            <a href="admlogin.php">Admin Login</a> | <a href="checkpnr.php">Check PNR</a>
        </div>
    </body>
</html>