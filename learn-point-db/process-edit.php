<?php

// Connect to MySQL
$connection = mysqli_connect("localhost", "root", "", "learn_gis");

// Receive data from POST
$id = $_POST['id'];
$name = $_POST['name'];
$lat = $_POST['lat'];
$lng = $_POST['lng'];

// Prepare a query to update data on table
$query = "UPDATE places SET name = '$name', location =  ST_GeomFromText('POINT($lat $lng)', 4326) WHERE id = $id";
var_dump($query);

// Send query
$result = mysqli_query($connection, $query);

// Back to Read All page
header("Location: read-all.php");
exit();

// Close MySQL connection
mysqli_close($connection);
