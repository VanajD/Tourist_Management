<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track All Members</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #myMap {
            width: 100%;
            height: 600px;
        }
    </style>
    <script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?key=AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR&callback=loadMap" async defer></script>
    <script>
        let map;

        function loadMap() {
            map = new Microsoft.Maps.Map('#myMap', {
                center: new Microsoft.Maps.Location(16.6062, 80.3321), // Default to Seattle
                zoom: 6
            });

            // Fetch the group members' locations from the server
            fetch('get_group_locations.php?group_id=' + getGroupId())
                .then(response => response.json())
                .then(data => {
                    data.forEach(member => {
                        const location = new Microsoft.Maps.Location(member.latitude, member.longitude);
                        const pushpin = new Microsoft.Maps.Pushpin(location, { title: member.name });
                        map.entities.push(pushpin);
                    });
                });
        }

        function getGroupId() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('group_id');
        }
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #007bff;">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">TravelApp</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h3 class="text-center">Tracking All Group Members</h3>
        <div id="myMap"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
