@extends('layouts.real')

@section('content')
<!-- Main content with Leaflet Map -->
<div class="row  py-4" style="padding-inline: 10px;">
    <div class="table-container col-md-4">
                <div class="mb-3">
                    <select class="form-select" id="status-filter" style="float: left;">
                        <option value="working">Working</option>
                        <option value="request maintenance">Request Maintenance</option>
                        <option value="under maintenance">Under Maintenance</option>
                    </select>
                </div>

                <!-- Table of streetlight data -->
                <table class="table table-bordered mt-5" id="streetlight-table">
                    <thead>
                        <tr>
                            <th class="text-center p-1">ID</th>
                            <th class="text-center p-1">Status</th>
                            <th class="text-center p-1">Ward</th>                            
                            <th class="text-center p-1">Rank</th>
                            <th class="text-center p-1" colspan="2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Streetlight data will be inserted here -->
                    </tbody>
                </table>
    </div>
    <div id="map" style="height: 500px;" class="col-md-8"></div> 
</div>
<div class="legend">
    <h4>Legend</h4>
    <div>
        <span class="color-box" style="background: red;"></span> Faulty
    </div>
    <div>
        <span class="color-box" style="background: green;"></span> Working
    </div>
</div>

<!-- Modal for Data Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Streetlights</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Import form for uploading files -->
                <form id="importForm" action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">Choose CSV File:</label>
                        <input class="form-control" type="file" id="file" name="file" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Import</button>
                    <div class="saving-message text-primary mt-2" id="import-saving-message">
                        Importing...
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- Edit Streetlight Modal -->
<div class="modal fade" id="editStreetlightModal" tabindex="-1" aria-labelledby="editStreetlightModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Streetlight</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-streetlight-form" action="{{ route('update.streetlight') }}" method="POST">
                    @csrf <!-- Include CSRF token for Laravel -->
                    <input type="hidden" id="edit-streetlight-id" name="streetlight_id">
                    <div class="mb-3">
                       <label for="edit-streetlight-name" class="form-label">Name</label>
                       <input id="edit-streetlight-name" class="form-control" required @if(Auth::user()->role !== 'admin') disabled @endif name='streetlight_name'>
                    </div>
                    <div class="mb-3" id="request-details">
                        <label for="edit-streetlight-description" class="form-label">Mantainence Request</label>
                        <textarea class="form-control" id="edit-streetlight-request-details" name="streetlight_request_details" disabled></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit-streetlight-status" class="form-label">Status</label>
                        <select class="form-select" id="edit-streetlight-status" name="streetlight_status">
                           <option value="working">Working</option>
                           <option value="request maintenance">Request Maintenance</option>
                           <option value="under maintenance">Under Maintenance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-streetlight-status" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit-streetlight-notes" name="streetlight_notes"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit-streetlight-energy-source" class="form-label">Energy source</label>
                        <select class="form-select" id="edit-streetlight-energy-source" name="streetlight_energy_source" @if(Auth::user()->role !== 'admin') disabled @endif>
                           <option value="grid">Grid</option>
                           <option value="solar">Solar</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-streetlight-crime-level" class="form-label">Crime level</label>
                        <select class="form-select" id="edit-streetlight-crime-level" name="streetlight_crime_level" @if(Auth::user()->role !== 'admin') disabled @endif >
                           <option value="low">Low</option>
                           <option value="moderate">Moderate</option>
                           <option value="high">High</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-streetlight-land-use" class="form-label">Land use</label>
                        <select class="form-select" id="edit-streetlight-land-use" name="streetlight_land_use" @if(Auth::user()->role !== 'admin') disabled @endif >
                        <option value="null">Not Available</option>
                           <option value="commercial">Commercial or Retail</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-streetlight-description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit-streetlight-description" name="streetlight_description" @if(Auth::user()->role !== 'admin') disabled @endif ></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <div class="saving-message text-primary mt-2" id="saving-message">
                        Saving...
                        <div class="spinner-border spinner-border-sm" role="status" id="loading-spinner">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsModalLabel">Rank lights</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="settings" action="{{ route('settings') }}" method="POST">
                    @csrf <!-- Include CSRF token for Laravel -->
                    <div class="mb-3">
                        <p>ARE SURE YOU YOU WANT TO ASSIGN MANTAINANCE PRIORITY LEVELS TO THE STREETLIGHTS?</p>
                    </div>
                    <button type="submit" class="btn btn-primary">Assign</button>
                    <div class="saving-message text-primary mt-2" id="assigning-message">
                        Assigning...
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Legend Modal -->
<div class="modal fade" id="legendModal" tabindex="-1" aria-labelledby="legendModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="legendModalLabel">Map Legend</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body legend">
                <div class="legend-item">
                    <div class="legend-icon" style="background-color: green;"></div>
                    <div>Working Streetlight</div>
                </div>
                <div class="legend-item">
                    <div class="legend-icon" style="background-color: red;"></div>
                    <div>Faulty Streetlight</div>
                </div>
                <!-- Add more legend items as needed -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
// Function to open the edit modal
   function openRequestModal(light) {
    console.log(light);
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
</script>


