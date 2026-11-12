<?php

// Connect to MySQL
$connection = mysqli_connect("localhost", "root", "", "learn_gis");

// Receive data from POST
$name = $_POST['name'];
$lat = $_POST['lat'];
$lng = $_POST['lng'];

// Prepare a query to insert data into table
$query = "INSERT INTO places (name, location) VALUES ('$name', ST_GeomFromText('POINT($lat $lng)', 4326))";
var_dump($query);
// Send query
// $result = mysqli_query($connection, $query);

// Close MySQL connection
mysqli_close($connection);

// Back to Read All page
header("Location: read-all.php");
exit();