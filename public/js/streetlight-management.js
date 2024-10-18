
// Initialize the map and set view
const map = L.map('map').setView([-18.018, 31.065], 14);

// Define base layers
const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 20,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
});

// Add the OSM layer to the map
osmLayer.addTo(map);

// Add streetlight markers and table logic
let streetlights = []; // Array to store streetlight data from API

// Fetch streetlight data from the Laravel API
fetch('/api/streetlights')
    .then(response => response.json())
    .then(data => {
        streetlights = data; // Store data in the array
        renderStreetlightTable(); // Render the table and markers
    })
    .catch(error => console.error('Error fetching streetlight data:', error));

// Function to render the streetlight table based on filter
function renderStreetlightTable(filter = 'working') {
    const tableBody = document.querySelector('#streetlight-table tbody');
    tableBody.innerHTML = ''; // Clear the table

    streetlights.forEach(light => {
        if (filter === light.status) {
            const coordinates = JSON.parse(light.location).coordinates;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="text-center p-1">${light.id}</td>
                <td class="text-center p-1">${light.status}</td>
                <td class="text-center p-1">${light.ward}</td>
                <td class="text-center p-1">${light.score}</td>
                <td class="text-center p-1"><a href="#" onclick="goToStreetlight(${coordinates[1]}, ${coordinates[0]})">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 90 90">
                <path d="M 45 90 c -1.415 0 -2.725 -0.748 -3.444 -1.966 l -4.385 -7.417 C 28.167 65.396 19.664 51.02 16.759 45.189 c -2.112 -4.331 -3.175 -8.955 -3.175 -13.773 C 13.584 14.093 27.677 0 45 0 c 17.323 0 31.416 14.093 31.416 31.416 c 0 4.815 -1.063 9.438 -3.157 13.741 c -2.961 5.909 -11.41 20.193 -20.353 35.309 l -4.382 7.413 C 47.725 89.252 46.415 90 45 90 z" fill="${light.status == 'working' ? 'green' : 'red'}" />
                <path d="M 45 45.678 c -8.474 0 -15.369 -6.894 -15.369 -15.368 S 36.526 14.941 45 14.941 c 8.474 0 15.368 6.895 15.368 15.369 S 53.474 45.678 45 45.678 z" fill="rgb(255,255,255)" />
                </svg>
                </a></td>
                <td class="text-center p-1"><a href="#" onclick='openEditModal(${JSON.stringify(light).replace(/'/g, "\\'")})'>
                <svg class="feather feather-edit" fill="none" height="15" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="15" xmlns="http://www.w3.org/2000/svg"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a></td>
            `;
            tableBody.appendChild(row);
        }
    });
}

// Function to zoom to a streetlight
function goToStreetlight(lat, lng) {
    map.setView([lat, lng], 17); // Set the map view to the streetlight location and zoom in
}

// Create an empty layer group for the streetlights
const streetlightLayer = L.layerGroup().addTo(map);

// Initialize an empty geoJSON layer for Chitungwiza wards (don't add it to the map yet)
let landUseLayer = L.geoJSON(null, {
    style: function(feature) {
        return {
            color: 'blue',  // Polygon border color
            weight: 2,      // Border thickness
            fillOpacity: 0.1  // Transparency of the fill
        };
    },
    onEachFeature: function(feature, layer) {
        // Bind popup for each ward with its properties (e.g., name)
        if (feature.properties) {
            layer.bindPopup(`<h6>Type: ${feature.properties.type}`);
        }
    }
});

// Fetch the GeoJSON for Chitungwiza wards and add it to the layer
fetch('/api/landuse-geojson')
    .then(response => response.json())
    .then(data => {
        landUseLayer.addData(data);  // Add GeoJSON data to the layer
    })
    .catch(error => console.error('Error loading GeoJSON:', error));


// Initialize an empty geoJSON layer for Chitungwiza wards (don't add it to the map yet)
let chitungwizaLayer = L.geoJSON(null, {
    style: function(feature) {
        return {
            color: '#777',  // Polygon border color
            weight: 2,      // Border thickness
            fillOpacity: 0.1  // Transparency of the fill
        };
    },
    onEachFeature: function(feature, layer) {
        // Bind popup for each ward with its properties (e.g., name)
        if (feature.properties && feature.properties.ADM3_EN) {
            layer.bindPopup(`<h6>Ward ${feature.properties.ADM3_EN} <br> Population density: ${feature.properties.Density}</h6>`);
        }
    }
});

// Fetch the GeoJSON for Chitungwiza wards and add it to the layer
fetch('/api/wards-geojson')
    .then(response => response.json())
    .then(data => {
        chitungwizaLayer.addData(data);  // Add GeoJSON data to the layer
    })
    .catch(error => console.error('Error loading GeoJSON:', error));

// Fetch streetlight data from the Laravel API
fetch('/api/streetlights')
    .then(response => response.json())
    .then(data => {
        data.forEach(light => {
            const coordinates = JSON.parse(light.location).coordinates;
            const marker = L.circleMarker([coordinates[1], coordinates[0]], {
                color: light.status == 'working' ? 'green' : 'red',
                fillColor: light.status == 'working' ? 'green' : 'red',
                fillOpacity: 1,
                zIndexOffset: 1000,
                radius: 7
            });
            console.log(light)
            const message = `<button class="btn" style="color: blue; font-size: 15px; padding: 0px; margin: 0px;" onclick="openRequestModal(${JSON.stringify(light).replace(/"/g, '&quot;')})">Manage</button>`;
            marker.bindPopup(`<h6>
              Streetlight ${light.id} <br> 
              Status: ${light.status} <br>
              ${message}</h6>`);
            streetlightLayer.addLayer(marker);
        });
    })
    .catch(error => console.error('Error fetching streetlight data:', error));

// Define base layers
const baseMaps = {
    "OpenStreetMap": osmLayer,
};

// Define overlay layers
const overlayMaps = {
    "Streetlights": streetlightLayer, // Layer for streetlights
    "Wards": chitungwizaLayer, // Layer for Chitungwiza polygons
    "Land Use": landUseLayer // Layer for Chitungwiza polygons
};

// Add control for base maps and overlays (initially show only base maps and streetlights)
L.control.layers(null, overlayMaps).addTo(map);





    function openEditModal(light) {
    // Populate the edit modal fields with the streetlight's current data
    document.getElementById('edit-streetlight-id').value = light.id;
    document.getElementById('edit-streetlight-name').value = light.name;
    document.getElementById('edit-streetlight-status').value = light.status;
    document.getElementById('edit-streetlight-notes').value = light.notes;
    document.getElementById('edit-streetlight-land-use').value = light.land_use;
    document.getElementById('edit-streetlight-description').value = light.description !== null ? light.description : 'Not available';
    document.getElementById('edit-streetlight-energy-source').value = light.energy_source;
    document.getElementById('edit-streetlight-crime-level').value =  light.crime_level;
    document.getElementById('request-details').style.display = light.status == 'working' ? 'none' : 'block';
    document.getElementById('edit-streetlight-request-details').value =  light.request_details;

    // Show the edit modal
    var editModal = new bootstrap.Modal(document.getElementById('editStreetlightModal'));
    editModal.show();
    }

    // Handle form submission to save changes
    document.getElementById('edit-streetlight-form').addEventListener('submit', function(event) {
        // Hide the Save Changes button and show loading message
        document.querySelector('#edit-streetlight-form button[type="submit"]').style.display = 'none';
        document.getElementById('saving-message').style.display = 'block';
        document.getElementById('loading-spinner').style.display = 'inline-block';
    });


    // Handle form submission to save changes
    document.getElementById('importForm').addEventListener('submit', function(event) {
        // Hide the Save Changes button and show loading message
        document.querySelector('#importForm button[type="submit"]').style.display = 'none';
        document.getElementById('import-saving-message').style.display = 'block';
    });


    // Handle form submission to save changes
    document.getElementById('settings').addEventListener('submit', function(event) {
        // Hide the Save Changes button and show loading message
        document.querySelector('#settings button[type="submit"]').style.display = 'none';
        document.getElementById('assigning-message').style.display = 'block';
    });


    // Filter dropdown
    document.getElementById('status-filter').addEventListener('change', function() {
        var filter = this.value;
        renderStreetlightTable(filter); // Render table based on selected filter
    });
    
