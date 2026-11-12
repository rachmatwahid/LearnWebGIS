<?php

// Connect to MySQL
$connection = mysqli_connect("localhost", "root", "", "learn_gis");

// Catch input from HTML
$name = $_POST['name'];
$wkt_polygon = $_POST['wkt_polygon'];

// Prepare a query
$query = "INSERT INTO areas (name, boundary) VALUES ('$name',ST_GeomFromText('$wkt_polygon',4326))";

// Send the query
mysqli_query($connection, $query);

// Close the connection
mysqli_close($connection);

// Back to Read All page
header("Location: read-all.php");
exit();