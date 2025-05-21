<?php
require "db.php";

$sql = "SELECT * FROM place_data";
$result = $conn->query($sql);

$places = [];
while ($row = $result->fetch_assoc()) {
    $places[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Microsoft Maps API Example</title>
    <style>
        #mapContainer { height: 550px; }
    </style>
</head>
<body style="background:#d9ffa8;">
    <h3>Select the Mapping Points</h3>
    <div id="mapContainer"></div>
    <input type="hidden" id="latitude" readonly>
    <input type="hidden" id="longitude" readonly>

    <form action="step2.php" method="GET">
        <center>
        <br>
            <button type="button" style="font-size:18px;padding:10px;border-radius:20px;background: linear-gradient(to right, #3498db, #2ecc71); color: white;" onclick="submitCoordinates()">Submit</button>
        </center>
    </form>

    <script type='text/javascript' src='https://www.bing.com/api/maps/mapcontrol?callback=loadMapScenario' async defer></script>
    <script>
        var savedCoordinates = [];
        var map;

        function loadMapScenario() {
            map = new Microsoft.Maps.Map(document.getElementById('mapContainer'), {
                credentials: 'AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR',
                center: new Microsoft.Maps.Location(15.9129, 79.7400),
                zoom: 8
            });

            // Load places data from PHP
            var places = <?php echo json_encode($places); ?>;

            places.forEach(function(place) {
                var location = new Microsoft.Maps.Location(parseFloat(place.latitude), parseFloat(place.longitude));
                var pushpin = new Microsoft.Maps.Pushpin(location, { title: place.place_name });
                map.entities.push(pushpin);
            });

            Microsoft.Maps.Events.addHandler(map, 'click', function (e) {
                document.getElementById('latitude').value = e.location.latitude;
                document.getElementById('longitude').value = e.location.longitude;

                var pushpin = new Microsoft.Maps.Pushpin(e.location, { color: 'red' });
                map.entities.push(pushpin);
                savedCoordinates.push({ latitude: e.location.latitude, longitude: e.location.longitude });
            });
        }

        function submitCoordinates() {
            var jsonCoordinates = JSON.stringify(savedCoordinates, null, 2);
            alert('JSON Coordinates:\n' + jsonCoordinates);

            var jsonInput = document.createElement('input');
            jsonInput.type = 'hidden';
            jsonInput.name = 'location';
            jsonInput.value = jsonCoordinates;
            document.querySelector('form').appendChild(jsonInput);
            document.querySelector('form').submit();
        }
    </script>
</body>
</html>