<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<style>
.table-container {
    overflow-y: auto; /* Enable vertical scrolling */
    border: 1px solid #ccc; /* Add border for visibility */
}

/* Adjust height for small screens */
@media (max-width: 576px) {
    .table-container {
        height: 300px; /* Smaller height for small screens */
    }
}

/* Adjust height for medium screens */
@media (min-width: 577px) and (max-width: 768px) {
    .table-container {
        height: 300px; /* Medium height for medium screens */
    }
}

</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Main content with Leaflet Map and Table -->
            <div class="lg:flex" style="height: 500px;">
                <!-- Table container, set different heights for smaller screens -->
                <div class="table-container lg:w-1/3 bg-white p-4 rounded">
                    <div class="mb-3">
                        <select class="block w-full px-3 py-2 border border-gray-300 rounded-md" id="status-filter">
                            <option value="working">Working</option>
                            <option value="request maintenance">Request Maintenance</option>
                            <option value="under maintenance">Under Maintenance</option>
                        </select>
                    </div>

                    <!-- Table of streetlight data -->
                    <table class="table-auto w-full text-center border-collapse border border-gray-300" style="height: 100%;">
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

                <div id="map" class="lg:w-2/3" style="height: 100%;"></div>
            </div>
        </div>
    </div>

    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="toggleModal()">Open Modal</button>

<!-- Modal and backdrop -->
<div id="modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
    <div class="flex justify-between mb-4">
      <h2 class="text-xl font-semibold">Modal Title</h2>
      <button onclick="toggleModal()">
        <svg class="w-6 h-6 text-gray-500 hover:text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <p class="text-gray-600 mb-4">This is a simple modal using Tailwind.</p>
    <div class="flex justify-end space-x-2">
      <button class="bg-gray-500 text-white px-3 py-1 rounded-md" onclick="toggleModal()">Cancel</button>
      <button class="bg-blue-500 text-white px-3 py-1 rounded-md">Confirm</button>
    </div>
  </div>
</div>
    
</x-app-layout>

<!-- Leaflet Scripts -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<!-- Custom Scripts -->
<script src="{{ asset('js/streetlight-management.js') }}"></script>
