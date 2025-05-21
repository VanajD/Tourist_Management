<?php
// No server-side PHP logic is required for this example.
// You could extend this with session management or database integration if needed.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Places & Hotels - Bing Maps</title>
    <script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?callback=loadMapScenario&key=AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR" async defer></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        html, body {
            height: 100%;
            background: #f0f2f5;
        }
        #mapContainer {
            width: 70%;
            height: 100%;
            float: right;
            border-left: 1px solid #ddd;
        }
        #searchPanel {
            width: 30%;
            height: 100%;
            float: left;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }
        #searchPanel h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .search-form {
            position: relative;
        }
        #locationInput {
            width: 100%;
            padding: 12px 40px 12px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s;
        }
        #locationInput:focus {
            border-color: #0078d4;
            box-shadow: 0 0 5px rgba(0, 120, 212, 0.3);
        }
        #categorySelect {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #fff;
            outline: none;
            cursor: pointer;
        }
        #searchButton {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            margin-top: 10px;
            background: #0078d4;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        #searchButton:hover {
            background: #005ea2;
        }
        #suggestions {
            position: absolute;
            width: 100%;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 5px;
        }
        .suggestion-item {
            padding: 10px;
            cursor: pointer;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
        }
        .suggestion-item:last-child {
            border-bottom: none;
        }
        .suggestion-item:hover {
            background: #f5f5f5;
        }
        #resultsList {
            margin-top: 20px;
        }
        .result-item {
            padding: 15px;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 5px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .result-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .result-item strong {
            color: #0078d4;
            font-size: 16px;
        }
        .result-item p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div id="mapContainer"></div>
    <div id="searchPanel">
        <h2>Explore Places & Hotels</h2>
        <div class="search-form">
            <input type="text" id="locationInput" placeholder="Enter a location (e.g., Seattle, WA)" oninput="getSuggestions()">
            <div id="suggestions"></div>
        </div>
        <select id="categorySelect">
            <option value="Places">Places (General)</option>
            <option value="Hotels">Hotels</option>
        </select>
        <button id="searchButton" onclick="searchPlaces()">Search</button>
        <div id="resultsList"></div>
    </div>

    <script>
        let map;
        let pushpins = [];
        const apiKey = 'AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR';

        function loadMapScenario() {
            if (typeof Microsoft === 'undefined' || typeof Microsoft.Maps === 'undefined') {
                console.error('Bing Maps API failed to load.');
                return;
            }

            // Initialize the map with a default center (Seattle, WA)
            map = new Microsoft.Maps.Map(document.getElementById('mapContainer'), {
                center: new Microsoft.Maps.Location(47.6062, -122.3321),
                zoom: 12,
                mapTypeId: Microsoft.Maps.MapTypeId.road
            });
        }

        async function getSuggestions() {
            const query = document.getElementById('locationInput').value.trim();
            const suggestionsDiv = document.getElementById('suggestions');

            if (query.length < 3) {
                suggestionsDiv.style.display = 'none';
                return;
            }

            try {
                const url = `https://dev.virtualearth.net/REST/v1/Autosuggest?query=${encodeURIComponent(query)}&maxResults=5&key=${apiKey}`;
                const response = await fetch(url);
                const data = await response.json();

                if (data.resourceSets.length === 0 || data.resourceSets[0].resources.length === 0) {
                    suggestionsDiv.style.display = 'none';
                    return;
                }

                const suggestions = data.resourceSets[0].resources[0].value;
                let suggestionsHtml = '';

                suggestions.forEach(suggestion => {
                    const address = suggestion.address.formattedAddress || suggestion.name;
                    suggestionsHtml += `
                        <div class="suggestion-item" onclick="selectSuggestion('${address}')">
                            ${address}
                        </div>
                    `;
                });

                suggestionsDiv.innerHTML = suggestionsHtml;
                suggestionsDiv.style.display = 'block';
            } catch (error) {
                console.error('Error fetching suggestions:', error);
                suggestionsDiv.style.display = 'none';
            }
        }

        function selectSuggestion(address) {
            document.getElementById('locationInput').value = address;
            document.getElementById('suggestions').style.display = 'none';
            searchPlaces(); // Trigger search on selection
        }

        async function searchPlaces() {
            const location = document.getElementById('locationInput').value.trim();
            const category = document.getElementById('categorySelect').value;

            if (!location) {
                alert('Please enter a location.');
                return;
            }

            // Clear previous results and pushpins
            clearMap();
            document.getElementById('resultsList').innerHTML = '';
            document.getElementById('suggestions').style.display = 'none';

            try {
                // Step 1: Geocode the location to get coordinates
                const geocodeUrl = `https://dev.virtualearth.net/REST/v1/Locations?q=${encodeURIComponent(location)}&key=${apiKey}`;
                const geocodeResponse = await fetch(geocodeUrl);
                const geocodeData = await geocodeResponse.json();

                if (geocodeData.resourceSets.length === 0 || geocodeData.resourceSets[0].resources.length === 0) {
                    alert('Location not found.');
                    return;
                }

                const coords = geocodeData.resourceSets[0].resources[0].point.coordinates;
                const lat = coords[0];
                const lon = coords[1];

                // Update map center
                map.setView({ center: new Microsoft.Maps.Location(lat, lon), zoom: 14 });

                // Step 2: Search for places or hotels
                const query = category === 'Hotels' ? 'hotels' : '';
                const searchUrl = `https://dev.virtualearth.net/REST/v1/LocalSearch/?query=${encodeURIComponent(query)}&userLocation=${lat},${lon}&maxResults=10&key=${apiKey}`;
                const searchResponse = await fetch(searchUrl);
                const searchData = await searchResponse.json();

                if (searchData.resourceSets.length === 0 || searchData.resourceSets[0].resources.length === 0) {
                    document.getElementById('resultsList').innerHTML = '<p>No results found.</p>';
                    return;
                }

                // Process and display results
                const results = searchData.resourceSets[0].resources;
                let resultsHtml = '';

                results.forEach((result, index) => {
                    const name = result.name;
                    const address = result.Address.formattedAddress;
                    const coords = result.Point.coordinates;
                    const lat = coords[0];
                    const lon = coords[1];

                    // Add to results list
                    resultsHtml += `
                        <div class="result-item" onclick="focusOnLocation(${lat}, ${lon}, '${name}')">
                            <strong>${name}</strong>
                            <p>${address}</p>
                        </div>
                    `;

                    // Add pushpin to map
                    const location = new Microsoft.Maps.Location(lat, lon);
                    const pushpin = new Microsoft.Maps.Pushpin(location, {
                        title: name,
                        subTitle: category,
                        color: category === 'Hotels' ? '#ff4500' : '#0078d4'
                    });
                    map.entities.push(pushpin);
                    pushpins.push(pushpin);
                });

                document.getElementById('resultsList').innerHTML = resultsHtml;
            } catch (error) {
                console.error('Error fetching data:', error);
                alert('An error occurred while searching. Please try again.');
            }
        }

        function clearMap() {
            pushpins.forEach(pin => map.entities.remove(pin));
            pushpins = [];
        }

        function focusOnLocation(lat, lon, name) {
            const location = new Microsoft.Maps.Location(lat, lon);
            map.setView({ center: location, zoom: 16 });
            const infobox = new Microsoft.Maps.Infobox(location, {
                title: name,
                visible: true,
                maxWidth: 200
            });
            map.entities.push(infobox);
            setTimeout(() => infobox.setOptions({ visible: false }), 5000); // Hide after 5 seconds
        }

        // Hide suggestions when clicking outside
        document.addEventListener('click', (e) => {
            const suggestionsDiv = document.getElementById('suggestions');
            const locationInput = document.getElementById('locationInput');
            if (!suggestionsDiv.contains(e.target) && e.target !== locationInput) {
                suggestionsDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>