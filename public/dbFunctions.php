<?php
function connect($dbHost, $dbName, $dbPort, $dbUser, $dbPassword){
    $conn = pg_pconnect("host=$dbHost port=$dbPort dbname=$dbName user=$dbUser password=$dbPassword");
    if(!$conn){
        echo "Couldn't connect to database!\n";
        exit;
    }
    return $conn;
}

function fetchAll($conn, $table){
    $query = "SELECT * FROM $table";
    $result = pg_query($conn, $query);
    $arr = pg_fetch_all($result);
    return $arr;
}

function BlockSQLInjection($str){
    $str = str_replace("'", "&quot;", $str);
    $str = str_replace("\"", "&dquot;", $str);
    $str = str_replace(",", "&com;", $str);
    $str = str_replace(";", "&semi;", $str);
    $str = str_replace(" ", "&nbsp;", $str);
    return $str;
}