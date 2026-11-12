<?php

// Catch place id from superglobal variable
$id = $_GET['id'];

// Connect to MySQL
$connection = mysqli_connect("localhost", "root", "", "learn_gis");

// Prepare a query to obtain a rows from table using id
$query = "SELECT id, name, ST_Y(location) as lng, ST_X(location) as lat FROM places WHERE id = $id LIMIT 1";

// Send the query to MySQL
$result = mysqli_query($connection, $query);

// Save the result into PHP array
$place = mysqli_fetch_assoc($result);

// Close the connection
mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit A Point</title>
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
    <a href="read-all.php">Back</a>
    <div id="map"></div>
    <form action="process-edit.php" method="post">
        <input type="hidden" id="id" name="id" value="<?= $place['id']; ?>">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= $place['name']; ?>">
        <br>
        <label for="lat">Latitude</label>
        <input type="text" id="lat" name="lat" value="<?= $place['lat']; ?>">
        <br>
        <label for="lng">Longitude</label>
        <input type="text" id="lng" name="lng" value="<?= $place['lng']; ?>">
        <br>
        <button type="reset">Reset</button>
        <button type="submit">Submit</button>
    </form>
</main>
<script>
    let center = [-0.0594, 109.3519];
    var map = L.map('map').setView(center, 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Save place data from PHP variable into JSON
    const place = <?= json_encode($place); ?>;

    // Show a marker of the place
    let marker = L.marker([place.lat, place.lng]).addTo(map);
    marker.bindPopup(`<b>${place.name}</b>`).openPopup();

    // Catch click event on map
    map.on('click', function(e){
        // Get coordinates
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        // Add as input form values
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
        if (marker) { // When a marker exists on map
            map.removeLayer(marker); // Remove old marker
        }
        marker = L.marker(e.latlng).addTo(map); // Add a new one
    });
</script>
</body>
</html>
