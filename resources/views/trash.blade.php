// Dummy streetlight data
    var streetlights = [
        { id: 1, name: 'Streetlight 1', status: 'working', location: [-17.993, 30.986] },
        { id: 2, name: 'Streetlight 2', status: 'faulty', location: [-17.994, 30.987] },
        { id: 3, name: 'Streetlight 3', status: 'working', location: [-17.995, 30.988] },
        { id: 4, name: 'Streetlight 4', status: 'faulty', location: [-17.996, 30.989] }
    ];

    // Function to render the list of streetlights
    function renderStreetlightList(filter = 'all') {
        var listContainer = document.getElementById('streetlight-list');
        listContainer.innerHTML = ''; // Clear the list before rendering

        streetlights.forEach(function(light) {
            if (filter === 'all' || filter === light.status) {
                var li = document.createElement('li');
                li.className = light.status;
                li.textContent = light.name + ' (' + light.status + ')';
                li.addEventListener('click', function() {
                    map.setView(light.location, 15); // Center map on the clicked streetlight
                });

                listContainer.appendChild(li);
            }
        });
    }

    // Render all streetlights by default
    renderStreetlightList();

    // Filter buttons
    document.getElementById('filter-all').addEventListener('click', function() {
        renderStreetlightList('all');
    });

    document.getElementById('filter-faulty').addEventListener('click', function() {
        renderStreetlightList('faulty');
    });

    document.getElementById('filter-working').addEventListener('click', function() {
        renderStreetlightList('working');
    });

    // Add streetlights as markers to the map
    streetlights.forEach(function(light) {
        var marker = L.marker(light.location).addTo(map)
            .bindPopup(light.name + ' (' + light.status + ')');
    });




    var populationToRadiusRatio = 0.01;  // Adjust this to scale the circle size appropriately

// Fetch population density data from API
fetch('/api/population-density')
    .then(response => response.json())
    .then(data => {
        data.forEach(function (point) {
            // Calculate circle radius based on population value
            var radius = point.population * populationToRadiusRatio;

            // Create a circle for each population point
            var circle = L.circle([point.latitude, point.longitude], {
                color: 'gray',
                fillColor: 'gray',
                fillOpacity: 0.5,
                radius: radius  // Radius is proportional to population
            });

            // Add the circle to the population density LayerGroup
            populationDensityLayer.addLayer(circle);

            // Optionally add a popup to show population value
            circle.bindPopup('Population: ' + point.population);
        });
    })
    .catch(error => {
        console.error('Error fetching population data:', error);
    });



    
    fetch('/api/streetlights')
        .then(response => response.json())
        .then(data => {
            data.forEach(function (light) {
                var marker = L.geoJSON(JSON.parse(light.location)).addTo(map);
                marker.bindPopup(light.name + '<br>Status: ' + light.status);
            });
        });