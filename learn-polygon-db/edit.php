<?php

// Catch place id from superglobal variable
$id = $_GET['id'];

// Connect to MySQL
$connection = mysqli_connect("localhost", "root", "", "learn_gis");

// Prepare a query to obtain a rows from table using id
$query = "SELECT id, name, ST_AsGeoJSON(boundary) as geojson, ST_AsText(boundary) as wkt FROM areas WHERE id = $id LIMIT 1";

// Send the query to MySQL
$result = mysqli_query($connection, $query);

// Save the result into PHP array
$area = mysqli_fetch_assoc($result);
$geojson = json_decode($area['geojson'], true);

// Extract array coordinates from geoJSON
$ring = $geojson['coordinates'][0];
// Remove last coordinates if it is the same as the first
if (count($ring) > 1) {
    array_pop($ring);
}

foreach($ring as $point) {
    $initial_coords[] = [$point[0],$point[1]];
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
    <title>Edit Polygon</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <style>
        #map {
            width: 640px;
            height: 360px;
        }
    </style>
</head>
<body>
<main>
    <div id="map"></div>
    <p id="info"></p>
    <form action="process-edit.php" method="post">
        <input type="hidden" id="id" name="id" value="<?= $area['id']; ?>">
        <label>Name
            <input type="text" name="name" id="name" value="<?= $area['name']; ?>">
        </label>
        <br>
        <label>Coordinates
            <textarea name="wkt_polygon" id="wkt_polygon" readonly></textarea>
        </label>
        <br>
        <button type="submit" id="submit">Submit</button>
    </form>
    <button onclick="resetMap()">Reset Map</button>
</main>
<script>

    // Convert PHP array into JSON
    const initCoords = <?php echo json_encode($initial_coords); ?>;
    let center = [initCoords[0][1], initCoords[0][0]];
    var map = L.map('map').setView(center, 18);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'OpenStreetMap'
    }).addTo(map);



    // Prepare an array for markers
    let markers = [];
    // Prepare a layer for polygon
    let polygonLayer = null;

    // Create an event listener on map
    map.on('click', function (e) {
        addMarker(e.latlng.lng, e.latlng.lat);
        updatePolygon();
    });

    // Load initial markers and polygon
    function loadInitialMarkers() {

        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        initCoords.forEach( point => {
            addMarker(point[0],point[1]);
        });

        updatePolygon();
    }

    // Create a new draggable marker
    function addMarker(lat,lng) {

        // Create draggable marker based on coordinates
        const marker = L.marker([lng, lat], {draggable: true}).addTo(map);

        // Insert marker into array
        markers.push(marker);

        // Update polygon after marker are dragged
        marker.on('dragend', updatePolygon);

        // Update polygon while marker are dragged
        marker.on('drag', updatePolygon);

        // Update polygon when user right click on marker
        marker.on('contextmenu', function () {
            map.removeLayer(marker);
            markers = markers.filter(m => m !== marker);
            updatePolygon();
        });
    }

    function updatePolygon() {
        // Collect all coordinates
        const coordinates = markers.map(m => {
            const position = m.getLatLng();
            return [position.lat, position.lng];
        });

        console.log(coordinates);

        // Show marker order when clicked
        markers.forEach((marker,index) => {
            marker.bindPopup(`${index}`);
        });

        // Hubungkan input form HTML untuk koordinat polygon
        const wktInput = document.getElementById('wkt_polygon');

        // Redraw polygon
        if (coordinates.length >= 3) {

            // Remove old polygon if it exists
            if (polygonLayer) {
                map.removeLayer(polygonLayer);
            }
            // Draw a new polygon
            polygonLayer = L.polygon(coordinates).addTo(map);



            const wktPoints = coordinates.map(pt => `${pt[0]} ${pt[1]}`);
            wktPoints.push(`${coordinates[0][0]} ${coordinates[0][1]}`);

            const wktString = `POLYGON((${wktPoints.join(', ')}))`;
            wktInput.value = wktString;
            // Activate submit button

        } else if (polygonLayer) {
            map.removeLayer(polygonLayer);
            polygonLayer = null;
        }
    }
    
    function resetMap() {
        loadInitialMarkers();
    }

    loadInitialMarkers();
</script>
</body>
</html>
