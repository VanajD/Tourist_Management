<?php
require "db.php";  // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Skip retrieving form data as per your request
    $location = json_decode($_POST['location'], true);  // Decode location JSON
    $adjMatrix = json_decode($_POST['adj_matrix'], true);  // Decode adjacency matrix JSON
    $placeNames = json_decode($_POST['place_names'], true);  // Decode place names JSON

    // Check if placeNames array is properly decoded
    if (!$placeNames || !is_array($placeNames)) {
        die("Invalid place names data");
    }

    // Retrieve the count of records from the 'place_data' table
    $countResult = $conn->query("SELECT COUNT(*) as count FROM place_data");
    if (!$countResult) {
        die("Error retrieving record count: " . $conn->error);
    }
    $row = $countResult->fetch_assoc();
    $placeCount = $row['count'];  // Store the count

    // Insert locations and adjacency matrix into the 'place_data' table
    foreach ($location as $index => $loc) {
        $latitude = $loc['lat'];
        $longitude = $loc['lon'];
        $nodePlaceName = isset($placeNames[$index + 1]) ? $placeNames[$index + 1] : 'Unknown';

        // Prepare the adjacency data for this place using one-based indexing
        $adjIndex = $index + 1;
        if (isset($adjMatrix[$adjIndex])) {
            // Add the place count to each adjacency numerically
            $adjacencies = array_map(function($adj) use ($placeCount) {
                return $adj + $placeCount;  // Add the count to each adjacency value
            }, $adjMatrix[$adjIndex]);
            $adjacencies = implode(",", $adjacencies);  // Convert adjacency list to a string
        } else {
            $adjacencies = '';  // If no adjacency data is found for this place
        }

        // Insert the location and its modified adjacency data into the place_data table
        $stmt = $conn->prepare("INSERT INTO place_data (place_name, latitude, longitude, adjacencies) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            die("SQL error: " . $conn->error);  // Display error if prepare() fails
        }

        $stmt->bind_param("ssds", $nodePlaceName, $latitude, $longitude, $adjacencies);
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
    }

    // Redirect to the View page after successful form submission
    header("Location: View.php");
    exit();
}

// Close the connection
$conn->close();
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bing Maps Example</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?callback=loadMapScenario&key=AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR" async defer></script>
    <style>
        html, body {
            padding: 0;
            margin: 0;
            height: 100%;
            display: flex;
            flex-direction: row;
            width: 100%;
        }

        /* Container for the two parts (map and content) */
        .container {
            display: flex;
            flex: 1;
            flex-direction: row;
        }

        /* Map on the left side */
        #myMap {
            width: 100%;
            height: 100%;
            background-color: #e6e6e6;
        }

        /* Content on the right side */
        .content {
            width: 30%;
            padding: 20px;
            background-color: #f4f4f4;
            overflow-y: auto;
        }

        #distances {
            position: fixed;
            bottom: 0;
            left: 0;
            background-color: white;
            padding: 10px;
            overflow: auto;
            max-height: 30%;
            width: 200px;
            z-index: 1;
            text-align: center;
        }

        /* Form styling */
        input[type="text"], input[type="number"], textarea {
            width: 70%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 15px;
            margin: 4px 2px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }

        .map-controls {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
        }

        /* Hidden elements for inputs */
        #locationsInput {
            display: none;
        }
    </style>
</head>
<body>
<div class="container"> 
      <div id="myMap"></div>
      <div class="content">
        <form action="#" method="POST">
        <label for="fromInput">From Point:</label>
        <br>
        <input type="number" id="fromInput" placeholder="Enter from point">
        <br>
        <label for="toInput">To Point:</label>
        <br>
        <input type="number" id="toInput" placeholder="Enter to point">
        <textarea id="locationsInput" placeholder="Enter locations array" rows="2" cols="50" name="location">
 <?php
// admin.php

if (isset($_GET['location'])) {
    // Decode the URL-encoded JSON data
    $jsonData = urldecode($_GET['location']);

    // Decode the JSON string into a PHP array
    $coordinates = json_decode($jsonData, true);

    // Transform the array to the desired format
    $formattedCoordinates = array_map(function($coord) {
        return array("lat" => $coord['latitude'], "lon" => $coord['longitude']);
    }, $coordinates);

    // Print the formatted data
    echo json_encode($formattedCoordinates, JSON_PRETTY_PRINT);
} else {
    echo "No JSON data received.";
}
?>
</textarea>
<input type="hidden" id="placeNamesInput" name="place_names">

        <input type="hidden" id="adjacencyInput"  placeholder='Enter adjacency information JSON like: {"1": [2, 3], "2": [1, 3, 4], ...}' name="adj_matrix">
        </form>
    <style>
        .adjacency-row {
            margin-bottom: 10px;
        }
    </style>

<h2>Adjacency Matrix </h2>

<div id="dynamicForm">
    <div class="adjacency-row">
        <label for="node-1">Node : 1</label>
        <input type="text" id="node-1" placeholder="Enter connected nodes (ex : 2,3)" />
        <input type="text" id="place-name-1" placeholder="Enter place name (optional)" />
    </div>
</div>
<button onclick="addNode()">Add Node</button>
<button onclick="generateJSON()">Save</button>

<script>
    let nodeCount = 1;

    function addNode() {
        nodeCount++;

        const formContainer = document.getElementById("dynamicForm");
        const newRow = document.createElement("div");
        newRow.classList.add("adjacency-row");

        const label = document.createElement("label");
        label.setAttribute("for", `node-${nodeCount}`);
        label.textContent = `Node : ${nodeCount}`;

        const input = document.createElement("input");
        input.type = "text";
        input.id = `node-${nodeCount}`;
        input.placeholder = "Enter connected nodes (ex: 2,3)";

        const placeNameInput = document.createElement("input");
        placeNameInput.type = "text";
        placeNameInput.id = `place-name-${nodeCount}`;
        placeNameInput.placeholder = "Enter place name";

        newRow.appendChild(label);
        newRow.appendChild(input);
        newRow.appendChild(placeNameInput);
        formContainer.appendChild(newRow);
    }

   function generateJSON() {
    const adjacencyData = {};
    const placeNames = {};
    const formContainer = document.getElementById("dynamicForm");

    for (let i = 0; i < formContainer.children.length; i++) {
        const nodeInput = formContainer.children[i].querySelector(`#node-${i + 1}`);
        const placeNameInput = formContainer.children[i].querySelector(`#place-name-${i + 1}`);

        if (nodeInput && placeNameInput) {
            const nodeId = i + 1;
            const connections = nodeInput.value.split(",").map(val => parseInt(val.trim())).filter(val => !isNaN(val));
            adjacencyData[nodeId] = connections;
            placeNames[nodeId] = placeNameInput.value.trim();
        }
    }

    document.getElementById("adjacencyInput").value = JSON.stringify(adjacencyData, null, 2);
    document.getElementById("placeNamesInput").value = JSON.stringify(placeNames, null, 2);
}

</script>



        <button onclick="drawShortestPath()" style="background-color: #4CAF50; color: white; padding: 10px 15px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 4px; border: none;">Draw Shortest Path</button>

  <button style="background-color: #008CBA; color: white; padding: 10px 15px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 4px; border: none;" id="submit">Submit</button>
    </div>
    <div id="distances"></div>
</div>

    <script type="text/javascript">
        function calculateCenter(locations) {
            const sumLat = locations.reduce((sum, loc) => sum + loc.lat, 0);
            const sumLon = locations.reduce((sum, loc) => sum + loc.lon, 0);

            const avgLat = sumLat / locations.length;
            const avgLon = sumLon / locations.length;

            return new Microsoft.Maps.Location(avgLat, avgLon);
        }

        function haversine(lat1, lon1, lat2, lon2) {
            const toRadians = (angle) => (angle * Math.PI) / 180;
            const dLat = toRadians(lat2 - lat1);
            const dLon = toRadians(lon2 - lon1);

            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const radius = 6371;
            const distance = radius * c * 1000;

            return distance;
        }

        function findShortestPathIndices(locations, from, to, adjacencyInfo) {
            const n = locations.length;

            // Build adjacency matrix from the adjacency information
            const adjacencyMatrix = Array.from({ length: n }, () => Array(n).fill(Infinity));
            for (const point in adjacencyInfo) {
                const pointIndex = parseInt(point) - 1;
                for (const connectedPoint of adjacencyInfo[point]) {
                    const connectedPointIndex = parseInt(connectedPoint) - 1;
                    const distance = haversine(locations[pointIndex].lat, locations[pointIndex].lon, locations[connectedPointIndex].lat, locations[connectedPointIndex].lon);
                    adjacencyMatrix[pointIndex][connectedPointIndex] = adjacencyMatrix[connectedPointIndex][pointIndex] = distance;
                }
            }

            const dist = Array(n).fill(Infinity);
            const visited = Array(n).fill(false);
            const path = Array(n).fill(-1);

            dist[from] = 0;

            for (let count = 0; count < n - 1; count++) {
                let u = -1;
                for (let i = 0; i < n; i++) {
                    if (!visited[i] && (u === -1 || dist[i] < dist[u])) {
                        u = i;
                    }
                }

                visited[u] = true;

                for (let v = 0; v < n; v++) {
                    if (!visited[v] && adjacencyMatrix[u][v] !== Infinity && dist[u] + adjacencyMatrix[u][v] < dist[v]) {
                        dist[v] = dist[u] + adjacencyMatrix[u][v];
                        path[v] = u;
                    }
                }
            }

            let current = to;
            const shortestPathIndices = [current];

            while (current !== from) {
                current = path[current];
                shortestPathIndices.push(current);
            }

            return { indices: shortestPathIndices.reverse(), cost: dist[to] };
        }

        function loadMapScenario() {
            var locationsInput = document.getElementById('locationsInput').value;
            var locations = JSON.parse(locationsInput);

            var centerLocation = calculateCenter(locations);
            var map = new Microsoft.Maps.Map(document.getElementById('myMap'), {
                mapTypeId: Microsoft.Maps.MapTypeId.aerial,
                center: centerLocation,
                zoom: 10
            });

            for (var i = 0; i < locations.length; i++) {
                var location = new Microsoft.Maps.Location(locations[i].lat, locations[i].lon);
                var pushpin = new Microsoft.Maps.Pushpin(location, {
                    title: 'Point ' + (i + 1),
                    htmlContent: '<div style="font-weight: bold; font-size: 12px; color: blue;">' + (i + 1) + '</div>',
                    color: 'blue'
                });
                map.entities.push(pushpin);
            }

            let distancesHTML = '';
            let shortestPathHTML = '';

            for (let i = 0; i < locations.length - 1; i++) {
                let short = 99999;
                let shortIndex = -1;

                for (let j = i + 1; j < locations.length; j++) {
                    const location1 = locations[i];
                    const location2 = locations[j];
                    const distance = haversine(location1.lat, location1.lon, location2.lat, location2.lon);

                    if (distance < short) {
                        short = distance;
                        shortIndex = j;
                    }

                    distancesHTML += `<p>${i + 1} <span class="material-symbols-outlined" style="font-size:12px;">sync_alt</span> ${j + 1}: ${distance.toFixed(2)} m</p>`;
                }

                shortestPathHTML += `(${i + 1},${shortIndex + 1}):${short.toFixed(2)}m`;
            }
            document.getElementById('distances').innerHTML = '<h4>Distances Between</h4>' + distancesHTML;
        }

        function drawShortestPath() {
            var locationsInput = document.getElementById('locationsInput').value;
            var locations = JSON.parse(locationsInput);
            var fromPoint = parseInt(document.getElementById('fromInput').value) - 1;
            var toPoint = parseInt(document.getElementById('toInput').value) - 1;
            var adjacencyInput = document.getElementById('adjacencyInput').value;
            var adjacencyInfo = JSON.parse(adjacencyInput);

            if (
                isNaN(fromPoint) ||
                isNaN(toPoint) ||
                fromPoint < 0 ||
                toPoint < 0 ||
                fromPoint >= locations.length ||
                toPoint >= locations.length ||
                !adjacencyInfo
            ) {
                alert('Invalid input for "From" or "To" points or adjacency information.');
                return;
            }

            var result = findShortestPathIndices(locations, fromPoint, toPoint, adjacencyInfo);
            var shortestPathIndices = result.indices;
            var shortestPathLocations = shortestPathIndices.map(index => new Microsoft.Maps.Location(locations[index].lat, locations[index].lon));

            var map = new Microsoft.Maps.Map(document.getElementById('myMap'), {
                mapTypeId: Microsoft.Maps.MapTypeId.aerial,
                center: shortestPathLocations[0],
                zoom: 18
            });

            var zoomLevel = 0;
            var maxZoom = 19;
            var zoomInterval = 200;

            function increaseZoom() {
                if (zoomLevel <= maxZoom) {
                    map.setView({ zoom: zoomLevel });
                    zoomLevel++;
                    setTimeout(increaseZoom, zoomInterval);
                }
            }


            for (var i = 0; i < locations.length; i++) {
                var location = new Microsoft.Maps.Location(locations[i].lat, locations[i].lon);
                var pushpin = new Microsoft.Maps.Pushpin(location, {
                    title: 'Point ' + (i + 1),
                    htmlContent: '<div style="font-weight: bold; font-size: 12px; color: blue;">' + (i + 1) + '</div>',
                    color: 'blue'
                });
                map.entities.push(pushpin);
            }
            var shortestPathPolyline = new Microsoft.Maps.Polyline(shortestPathLocations, {
                strokeColor: 'green',
                strokeThickness: 3
            });

            map.entities.push(shortestPathPolyline);
            console.log("Total Cost:"+result.cost.toFixed(2)+"meters");
        }
    document.getElementById('submit').addEventListener('click', function () {
        // Manually submit the form
        document.querySelector('form').submit();
    });
        </script>
</body>
</html>
