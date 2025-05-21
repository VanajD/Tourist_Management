<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Location</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #myMap {
            width: 100%;
            height: 600px;
        }
    </style>
    <script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?key=AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR&callback=loadMap" async defer></script>
    <script>
        let map, userPushpin, userLatitude, userLongitude;

        // Load the map
        function loadMap() {
            map = new Microsoft.Maps.Map('#myMap', {
                center: new Microsoft.Maps.Location(47.6062, -122.3321),  // Default to Seattle coordinates
                zoom: 15
            });

            // Get the phone from the query parameter
            const urlParams = new URLSearchParams(window.location.search);
            const phone = urlParams.get('phone');

            // Simulating fetch location of the user from your server (for real implementation you can fetch from DB)
            fetchLocationFromServer(phone);
        }

        function fetchLocationFromServer(phone) {
            // Fetch user's location using phone number (you need to implement this part to get latitude and longitude from your DB)
            fetch(`get_user_location.php?phone=${phone}`)
                .then(response => response.json())
                .then(data => {
                    userLatitude = data.latitude;
                    userLongitude = data.longitude;

                    const location = new Microsoft.Maps.Location(userLatitude, userLongitude);

                    if (!userPushpin) {
                        userPushpin = new Microsoft.Maps.Pushpin(location, {
                            title: "User Location"
                        });
                        map.entities.push(userPushpin);
                    } else {
                        userPushpin.setLocation(location);
                    }

                    // Center map to the user's current position
                    map.setView({ center: location, zoom: 15 });

                    // Continuously track the location (This can be updated using a setInterval or websockets)
                    setInterval(() => {
                        fetchLocationFromServer(phone); // Re-fetch every X seconds
                    }, 5000);
                });
        }
    </script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #007bff;">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">TravelApp</a>
        </div>
    </nav>

    <!-- Track Location Map -->
    <div class="container mt-5">
        <h3 class="text-center">Tracking User Location</h3>
        <div id="myMap"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
