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
if(!empty($_SESSION['email'])){
    header("Location: agent.php", TRUE, 301);
    exit();
}

if(!empty($_POST)){
    try{
        $id = BlockSQLInjection($_POST['usr']);
        $password = BlockSQLInjection($_POST['pass']);
        $query = "SELECT name FROM booking_agents WHERE email = '$id' AND password = '$password'";
        $result = pg_query($db, $query);
        $result = pg_fetch_row($result);
        if($result){
            $_SESSION['name'] = $result[0];
            $_SESSION['email'] = $id;
            header("Location: agent.php", TRUE, 301);
            exit();
        }else{
            header("Location: index.php?msg=Invalid credentials!", TRUE, 301);
            exit();
        }
    }catch(exception $e){
        header("Location: index.php?msg=Some error occurred!", TRUE, 301);
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
        <form action="index.php" method="POST">
            <input name="usr" type="email" id="username" placeholder="Email ID">
            <input name="pass" type="password" id="password" placeholder="Password">
            <input type="submit" id="loginbtn" value="LOGIN">
        </form>
        <center>Not a member? <a href="signup.php" style="color: orangered;">Signup</a><center>
        <div id="admlgin">
            <a href="admlogin.php">Admin Login</a> | <a href="checkpnr.php">Check PNR</a>
        </div>
    </body>
</html>