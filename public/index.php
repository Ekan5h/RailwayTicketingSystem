<!DOCTYPE html>
<head>
<title>Railway Index</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
li {listt-style: none;}
</style>
</head>
<body>
<h2>Railway booking</h2>
<ul>
<form name="insert" action="insert.php" method="POST" >
<li>Dummy field</li><li><input type="text" name="dummy" /></li>
<li><input type="submit" /></li>
</form>
</ul>
List all trains-->
</body>
</html>
<?php
$conn = pg_pconnect("host=4.tcp.ngrok.io port=19681 dbname=railway user=postgres password=967967");
if (!$conn) {
  echo "An error occurred.\n";
  exit;
}

$result = pg_query($conn, "SELECT * from trains");

    
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

while ($row = pg_fetch_row($result)) {
    echo "ID: $row[0]  Name: $row[1]";
    echo "<br />\n";
}

?>