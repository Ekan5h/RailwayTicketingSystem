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
if(!empty($_SESSION['admin'])){
    header("Location: trainsched.php", TRUE, 301);
    exit();
}

if(!empty($_POST)){
    try{
        $password = BlockSQLInjection($_POST['pass']);
        if($password=="1234"){
            $_SESSION['admin'] = 1;
            header("Location: trainsched.php", TRUE, 301);
            exit();
        }else{
            header("Location: admlogin.php?msg=Invalid credentials!", TRUE, 301);
            exit();
        }
    }catch(exception $e){
        header("Location: admlogin.php?msg=Some error occurred!", TRUE, 301);
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
        <form action="admlogin.php" method="POST">
            <input name="pass" type="password" id="password" placeholder="Password">
            <input type="submit" id="loginbtn" value="LOGIN">
        </form>
    </body>
</html>