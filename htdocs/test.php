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
