<?php

// Connect to MySQL
$connection = mysqli_connect("localhost", "root", "", "learn_gis");

// Prepare a query to obtain all rows from table
$query = "SELECT id, name, ST_Y(location) as lat, ST_X(location) as lng FROM places";

// Send the query to MySQL
$result = mysqli_query($connection, $query);

// Save the result into PHP array
$places = [];
while ($row = mysqli_fetch_assoc($result)) {
    $places[] = $row;
}

// Close the connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Read All Points</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <style>
        #map {
            width: 426px;
            height: 240px;
        }
    </style>
</head>
<body>
<main>
    <div id="map"></div>
    <?php foreach($places as $place): ?>
    <?= $place['name']; ?>
        <a href="edit.php?id=<?= $place['id']; ?>">View</a>
        <br>
    <?php endforeach; ?>
    <a href="add.html">Add New</a>

</main>
<script>
    let center = [-0.0594, 109.3519];
    var map = L.map('map').setView(center, 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const places = <?php echo json_encode($places); ?>;
    places.forEach(function(place){
        const marker = L.marker([place.lng,place.lat]).addTo(map);
        marker.bindPopup(`<small>${place.name}</small>`);
    });
</script>
</body>
</html>
