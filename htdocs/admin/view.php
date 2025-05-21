
<?php
require "db.php";

// Query to get the data from the location_metadata table
$sql = "SELECT id, latitude, longitude, image_path, description FROM location_metadata";
$result = $conn->query($sql);

// Create an array to store the locations
$locations = [];
if ($result->num_rows > 0) {
    // Fetch all rows from the result
    while($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
} else {
    echo "No records found";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bing Maps with Shortest Path</title>
    <script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?callback=loadMapScenario&key=AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR" async defer></script>
    <style>
        html, body {
            padding: 0;
            margin: 0;
            height: 100%;
        }
        #mapContainer {
            width: 100%;
            height: 100%;
        }
        #controls {
            align-items: center;
            background: #f5f5f5;
            padding: 10px;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 0;
            left: 0;
            z-index: 9999;
            width: 300px;
            overflow-y: auto;
        }
        .district-btn {
            display: block;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            font-size: 16px;
            text-align: center;
            cursor: pointer;
            margin-bottom: 5px;
            border: none;
            width: 100%;
        }

        .district-dropdown {
            display: none;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            padding: 10px;
        }

        .district-dropdown input[type="checkbox"] {
            margin-right: 10px;
        }

        #checkboxes {
            margin-top: 10px;
        }

        .checkbox-group {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .checkbox-group label {
            font-size: 16px;
            margin-left: 5px;
        }
        button {
            padding: 10px;
            font-size: 16px;
            margin-top: 20px;
            width: 100%;
        }
        #results {
            margin-top: 20px;
        }
        #results table {
            width: 100%;
            border-collapse: collapse;
        }
        #results th, #results td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        #results th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

    <div id="mapContainer"></div>
    <div id="controls">
        <!-- District buttons with dropdown will be populated here -->
        <div id="districtsContainer"></div>
        <div id="checkboxes"></div>
        <button onclick="findShortestPath()">Find Shortest Path</button>
        <div id="results"></div>
    </div>

    <textarea id="dataInput" style="display:none;">
    <?php
session_start();
require "db.php";

if (!isset($_SESSION['email'])) {
    header("Location: signin.php");
    exit();
}

// Query to join place_data with location to get the district from the location table
$placeQuery = "
    SELECT p.id, p.place_name, p.latitude, p.longitude, p.adjacencies, l.district 
    FROM place_data p
    LEFT JOIN location l ON p.place_name = l.city
";
$placeResult = $conn->query($placeQuery);
$places = [];

if ($placeResult && $placeResult->num_rows > 0) {
    while ($placeRow = $placeResult->fetch_assoc()) {
        $places[] = [
            'id' => (int) $placeRow['id'],
            'name' => $placeRow['place_name'],
            'lat' => (float) $placeRow['latitude'],
            'lon' => (float) $placeRow['longitude'],
            'adjacencies' => array_map('intval', explode(',', $placeRow['adjacencies'])),
            'district' => $placeRow['district'],
        ];
    }
}

echo json_encode($places);

$conn->close();
?>

    </textarea>

    <script>
        let map, pushpins = [], polyLines = [];
        let locations = [];
        let districts = {};

        function loadMapScenario() {
            const dataInput = document.getElementById('dataInput').value;
            locations = JSON.parse(dataInput);

            // Populate the districts buttons and dropdowns
            locations.forEach(loc => {
                if (loc.district && !districts[loc.district]) {
                    districts[loc.district] = true;
                    
                    const districtButton = document.createElement('button');
                    districtButton.classList.add('district-btn');
                    districtButton.textContent = loc.district;

                    const districtDropdown = document.createElement('div');
                    districtDropdown.classList.add('district-dropdown');

                    locations.forEach(location => {
                        if (location.district === loc.district) {
                            const checkboxGroup = document.createElement('div');
                            checkboxGroup.classList.add('checkbox-group');

                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.id = `village${location.id}`;
                            checkbox.value = location.id;

                            const label = document.createElement('label');
                            label.setAttribute('for', checkbox.id);
                            label.textContent = location.name;

                            checkboxGroup.appendChild(checkbox);
                            checkboxGroup.appendChild(label);
                            districtDropdown.appendChild(checkboxGroup);
                        }
                    });

                    districtButton.onclick = () => {
                        districtDropdown.style.display = districtDropdown.style.display === 'block' ? 'none' : 'block';
                    };

                    document.getElementById('districtsContainer').appendChild(districtButton);
                    document.getElementById('districtsContainer').appendChild(districtDropdown);
                }
            });

            map = new Microsoft.Maps.Map(document.getElementById('mapContainer'), {
                mapTypeId: Microsoft.Maps.MapTypeId.road,
                zoom: 14,
                center: new Microsoft.Maps.Location(locations[0].lat, locations[0].lon)
            });

              // Define an array of locations to be displayed on the map
              var loc = <?php echo json_encode($locations); ?>;

// Iterate through the locations and add pushpins
loc.forEach(function(loc) {
    var location = new Microsoft.Maps.Location(loc.latitude, loc.longitude);
    
    // Create a pushpin with custom content
    var pushpin = new Microsoft.Maps.Pushpin(location, {
        title:loc.description
    });

    // Create an infobox to display the image and description
    var infobox = new Microsoft.Maps.Infobox(location, {
        description: '<img src="./../'+ loc.image_path + '" width="50" height="50"/>',
        visible: true
    });

    // Show infobox on mouseover and hide it on mouseout
    Microsoft.Maps.Events.addHandler(pushpin, 'mouseover', function (e) {
        infobox.setOptions({ visible: true });
    });

    Microsoft.Maps.Events.addHandler(pushpin, 'mouseout', function (e) {
        infobox.setOptions({ visible: false });
    });

    // Add pushpin and infobox to the map
    map.entities.push(pushpin);
    map.entities.push(infobox);
});
        }

        function findShortestPath() {
            const selectedVillages = Array.from(document.querySelectorAll('#districtsContainer input:checked')).map(checkbox => parseInt(checkbox.value));

            if (selectedVillages.length < 2) {
                alert('Please select at least two villages.');
                return;
            }

            clearPreviousMarkersAndPaths();
            const graph = buildWeightedGraph(locations);

            let resultsHtml = '<table><thead><tr><th>From</th><th>To</th><th>Cost</th></tr></thead><tbody>';

            selectedVillages.forEach((start, index) => {
                for (let i = index + 1; i < selectedVillages.length; i++) {
                    const end = selectedVillages[i];
                    const path = dijkstra(graph, start, end);

                    if (path) {
                        const cost = path.reduce((sum, current, idx) => {
                            if (idx === 0) return sum;
                            const prev = path[idx - 1];
                            return sum + graph[prev][current];
                        }, 0);

                        resultsHtml += `<tr><td>${locations.find(loc => loc.id === start).name}</td><td>${locations.find(loc => loc.id === end).name}</td><td>${cost.toFixed(2)} km</td></tr>`;
                        drawPath(map, locations, path);
                        markSelectedPoints(locations, start, end);
                    }
                }
            });
            document.getElementById('results').innerHTML = resultsHtml;
        }

        function clearPreviousMarkersAndPaths() {
            if (pushpins.length > 0) {
                pushpins.forEach(pin => map.entities.remove(pin));
                pushpins = [];
            }

            if (polyLines.length > 0) {
                polyLines.forEach(line => map.entities.remove(line));
                polyLines = [];
            }
        }

        function markSelectedPoints(locations, from, to) {
            const fromLoc = locations.find(loc => loc.id === from);
            const toLoc = locations.find(loc => loc.id === to);

            const fromPin = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(fromLoc.lat, fromLoc.lon), { title: fromLoc.name });
            const toPin = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(toLoc.lat, toLoc.lon), { title: toLoc.name });

            map.entities.push(fromPin);
            map.entities.push(toPin);

            pushpins.push(fromPin);
            pushpins.push(toPin);
        }

        function haversine(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const toRad = angle => (angle * Math.PI) / 180;
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function buildWeightedGraph(locations) {
            const graph = {};
            locations.forEach(loc => {
                graph[loc.id] = {};
                loc.adjacencies.forEach(neighborId => {
                    const neighbor = locations.find(l => l.id === neighborId);
                    const distance = haversine(loc.lat, loc.lon, neighbor.lat, neighbor.lon);
                    graph[loc.id][neighborId] = distance;
                });
            });
            return graph;
        }

        function dijkstra(graph, start, end) {
            const distances = {};
            const prev = {};
            const pq = [];

            for (const node in graph) {
                distances[node] = Infinity;
                prev[node] = null;
            }
            distances[start] = 0;
            pq.push({ node: start, priority: 0 });

            while (pq.length > 0) {
                pq.sort((a, b) => a.priority - b.priority);
                const { node: current } = pq.shift();

                if (current == end) {
                    const path = [];
                    let step = current;
                    while (step !== null) {
                        path.unshift(parseInt(step));
                        step = prev[step];
                    }
                    return path;
                }

                for (const neighbor in graph[current]) {
                    const newDist = distances[current] + graph[current][neighbor];
                    if (newDist < distances[neighbor]) {
                        distances[neighbor] = newDist;
                        prev[neighbor] = current;
                        pq.push({ node: neighbor, priority: newDist });
                    }
                }
            }

            return null;
        }

        function drawPath(map, locations, path) {
            const coordinates = path.map(id => {
                const loc = locations.find(l => l.id === id);
                return new Microsoft.Maps.Location(loc.lat, loc.lon);
            });
            const polyline = new Microsoft.Maps.Polyline(coordinates, { strokeColor: 'blue', strokeThickness: 5 });
            map.entities.push(polyline);
            polyLines.push(polyline);
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bing Maps Display</title>
    <script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?key=AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR" async defer></script>
    <script type="text/javascript">
        var map;

        function initialize() {
            // Create a map centered at a default location
            map = new Microsoft.Maps.Map('#myMap', {
                center: new Microsoft.Maps.Location(16.7542784, 78.020608), // Default center
                zoom: 10
            });

            // Define an array of locations to be displayed on the map
            var locations = <?php echo json_encode($locations); ?>;

            // Iterate through the locations and add pushpins
            locations.forEach(function(loc) {
                var location = new Microsoft.Maps.Location(loc.latitude, loc.longitude);
                
                // Create a pushpin with custom content
                var pushpin = new Microsoft.Maps.Pushpin(location, {
                    title: 'ID: ' + loc.id
                });

                // Create an infobox to display the image and description
                var infobox = new Microsoft.Maps.Infobox(location, {
                    description: '<img src="' + loc.image_path + '" width="100" height="100"/><br/>',
                    visible: true
                });

                // Show infobox on mouseover and hide it on mouseout
                Microsoft.Maps.Events.addHandler(pushpin, 'mouseover', function (e) {
                    infobox.setOptions({ visible: true });
                });

                Microsoft.Maps.Events.addHandler(pushpin, 'mouseout', function (e) {
                    infobox.setOptions({ visible: false });
                });

                // Add pushpin and infobox to the map
                map.entities.push(pushpin);
                map.entities.push(infobox);
            });
        }

        // Initialize the map when the page loads
        window.onload = initialize;
    </script>
</head>
<body>
    <div id="myMap" style="width: 100%; height: 800px;"></div>
</body>
</html>
