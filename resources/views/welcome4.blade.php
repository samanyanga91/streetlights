<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streetlight Management</title>
    <link rel="shortcut icon" href="/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <style>
        /* Legend modal styling */
        .legend {
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #fff;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .legend-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        /* Loading spinner styling */
        .import-spinner-border,
        .spinner-border {
            display: none;
            /* Hidden by default */
        }

        /* Saving message */
        .assigning-message,
        .import-saving-message,
        .saving-message {
            display: none;
            /* Hidden by default */
        }

        /* Modal styling */
        .modal-dialog {
            margin: 1.75rem auto;
        }

        #toast-body {
            font-weight: bold;
        }

        .table-container {
            height: 500px;
            /* Default height for larger screens */
            overflow-y: auto;
            /* Enable vertical scrolling */
            border: 1px solid #ccc;
            /* Add border for visibility */
            padding: 10px;
        }

        /* Adjust height for small screens */
        @media (max-width: 576px) {
            .table-container {
                height: 300px;
                /* Smaller height for small screens */
            }
        }

        /* Adjust height for medium screens */
        @media (min-width: 577px) and (max-width: 768px) {
            .table-container {
                height: 300px;
                /* Medium height for medium screens */
            }
        }

        .info {
            padding: 6px 8px;
            font: 14px/16px Arial, Helvetica, sans-serif;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }
    </style>
</head>

<body>

<nav class="bg-gray-800 p-4">
    <div class="flex justify-between items-center">
        <!-- Logo -->
        <a class="text-white font-bold text-lg" href="#">
            Streetlight Management
        </a>

        <!-- Collapse Button for mobile views -->
        <button class="text-white block lg:hidden" type="button" id="navbar-toggle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Links, Import Button and Profile Dropdown -->
        <div class="hidden lg:flex space-x-4 ml-auto" id="navbarNav">
            <a class="text-white" href="#">Home</a>
            <a class="text-white" href="#">Users</a>
            <button class="text-white" type="button" id="legendModalToggle">Legend</button>
            <button class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded" type="button"
                id="importModalToggle">Import Data</button>
            <button class="bg-white-500 hover:bg-blue-600 py-2 px-4 rounded" type="button"
                id="rankModalToggle">Rank Lights</button>
            <button class="bg-transparent text-white" type="button" id="settingsModalToggle">
                <img src="/gear.png" alt="settings" width="20" height="20" class="inline-block">
            </button>

            <!-- Profile Dropdown -->
            <div class="relative">
                <button class="text-white" id="profileDropdownToggle">
                    <img src="/profile.png" alt="Profile" width="30" height="30" class="inline-block rounded-full">
                </button>
                <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10" id="profileDropdown">
                    <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                    <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Logout</a>
                </div>
            </div>
        </div>
    </div>
</nav>

    <!-- Main content with Leaflet Map -->
    <div class="lg:flex p-4">
        <div class="table-container lg:w-1/3 bg-white shadow-lg p-4 rounded">
            <div class="mb-3">
                <select class="block w-full px-3 py-2 border border-gray-300 rounded-md" id="status-filter">
                    <option value="working">Working</option>
                    <option value="request maintenance">Request Maintenance</option>
                    <option value="under maintenance">Under Maintenance</option>
                </select>
            </div>

            <!-- Table of streetlight data -->
            <table class="table-auto w-full text-center border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="p-2 border border-gray-300">ID</th>
                        <th class="p-2 border border-gray-300">Status</th>
                        <th class="p-2 border border-gray-300">Ward</th>
                        <th class="p-2 border border-gray-300">Rank</th>
                        <th class="p-2 border border-gray-300" colspan="2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Streetlight data will be inserted here -->
                </tbody>
            </table>
        </div>
        <div id="map" class="lg:w-2/3 h-96 lg:h-auto"></div>
    </div>

    <!-- Modal for Data Import -->
    <div class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center" id="importModal">
        <div class="bg-white p-6 rounded shadow-lg">
            <h5 class="text-lg font-bold mb-4">Import Streetlights</h5>
            <form id="importForm" action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="file" class="block text-sm font-medium text-gray-700">Choose CSV</label>
                    <input class="block w-full px-3 py-2 border border-gray-300 rounded-md" type="file" id="file"
                        name="file" required>
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">Import</button>
                <div class="import-saving-message text-blue-500 mt-2" id="import-saving-message">
                    Importing...
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
    // Initialize the map
    const map = L.map('map').setView([-18.018, 31.065], 13);
    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });
    osmLayer.addTo(map);

    // Modals
    const importModal = document.getElementById('importModal');
    const settingsModal = document.getElementById('settingsModal');
    const legendModal = document.getElementById('legendModal');

    // Function to open modals
    function openModal(modal) {
        modal.classList.remove('hidden');
    }

    // Function to close modals
    function closeModal(modal) {
        modal.classList.add('hidden');
    }

    // Event listeners for opening modals
    document.getElementById('importModalToggle').addEventListener('click', () => {
        openModal(importModal);
    });

    document.getElementById('settingsModalToggle').addEventListener('click', () => {
        openModal(settingsModal);
    });

    document.getElementById('legendModalToggle').addEventListener('click', () => {
        openModal(legendModal);
    });

    // Event listeners for closing modals
    document.getElementById('importModalClose').addEventListener('click', () => {
        closeModal(importModal);
    });

    document.getElementById('settingsModalClose').addEventListener('click', () => {
        closeModal(settingsModal);
    });

    document.getElementById('legendModalClose').addEventListener('click', () => {
        closeModal(legendModal);
    });

    // Fetch streetlight data and populate map
    let streetlights = [];

    fetch('/api/streetlights')
        .then(response => response.json())
        .then(data => {
            streetlights = data;
            renderStreetlightTable(); // Call to render streetlight data in the table
            addStreetlightMarkers(); // Call to add markers to the map
        })
        .catch(error => console.error('Error fetching streetlight data:', error));

    // Function to render streetlight table based on status filter
    function renderStreetlightTable(filter = 'working') {
        const tableBody = document.querySelector('#streetlight-table tbody');
        tableBody.innerHTML = ''; // Clear previous entries

        streetlights.forEach(light => {
            if (filter === light.status) {
                const coordinates = JSON.parse(light.location).coordinates;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="p-2 border border-gray-300">${light.id}</td>
                    <td class="p-2 border border-gray-300">${light.status}</td>
                    <td class="p-2 border border-gray-300">${light.ward}</td>
                    <td class="p-2 border border-gray-300">${light.score}</td>
                    <td class="p-2 border border-gray-300">
                        <a href="#" onclick="goToStreetlight(${coordinates[1]}, ${coordinates[0]})">Go</a>
                    </td>
                    <td class="p-2 border border-gray-300">
                        <a href="#" onclick='openEditModal(${JSON.stringify(light).replace(/'/g, "\\'")})'>Edit</a>
                    </td>
                `;
                tableBody.appendChild(row);
            }
        });
    }

    // Function to zoom to a specific streetlight on the map
    function goToStreetlight(lat, lng) {
        map.setView([lat, lng], 17); // Set the map view to the streetlight location and zoom in
    }

    // Function to add markers to the map
    function addStreetlightMarkers() {
        streetlights.forEach(light => {
            const coordinates = JSON.parse(light.location).coordinates;
            const marker = L.circleMarker([coordinates[1], coordinates[0]], {
                color: light.status === 'working' ? 'green' : 'red',
                fillColor: light.status === 'working' ? 'green' : 'red',
                fillOpacity: 1,
                zIndexOffset: 1000,
                radius: 7
            });

            marker.bindPopup(`${light.name} <br>Status: ${light.status} <br><button onclick="openEditModal(${JSON.stringify(light).replace(/'/g, "\\'")})" class="text-blue-500 underline">Edit</button>`);
            marker.addTo(map);
        });
    }

    // Function to open the edit modal
    function openEditModal(light) {
        document.getElementById('edit-streetlight-id').value = light.id;
        document.getElementById('edit-streetlight-name').value = light.name;
        document.getElementById('edit-streetlight-status').value = light.status;
        document.getElementById('edit-streetlight-notes').value = light.notes;
        document.getElementById('edit-streetlight-land-use').value = light.land_use;
        document.getElementById('edit-streetlight-description').value = light.description || 'Not available';
        document.getElementById('edit-streetlight-energy-source').value = light.energy_source;
        document.getElementById('edit-streetlight-crime-level').value = light.crime_level;

        // Show the edit modal
        const editModal = document.getElementById('editStreetlightModal');
        editModal.classList.remove('hidden');

    // Toggle the navbar on mobile
    document.getElementById('navbar-toggle').addEventListener('click', function() {
        document.getElementById('navbarNav').classList.toggle('hidden');
    });

    // Toggle the profile dropdown
    document.getElementById('profileDropdownToggle').addEventListener('click', function() {
        document.getElementById('profileDropdown').classList.toggle('hidden');
    });


    }
</script>


</body>

</html>
