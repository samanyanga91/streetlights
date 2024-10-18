@extends('layouts.real')

@section('content')
<div id="map" style="height: 100vh;"></div> 

<div class="legend">
    <h4>Legend</h4>
    <div>
        <span class="color-box" style="background: red;"></span> Faulty
    </div>
    <div>
        <span class="color-box" style="background: green;"></span> Working
    </div>
</div>


<!-- Edit Streetlight Modal -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestModalLabel">Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="request-form" action="{{ route('post.request') }}" method="POST">
                    @csrf <!-- Include CSRF token for Laravel -->
                    <input type="hidden" id="streetlight-id" name="streetlight_id">
                    <div class="mb-3">
                        <label for="request" class="form-label">Details</label>
                        <textarea class="form-control" id="addition" name="details">Streetlight not working</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" id="submitButton">Create</button>
                    <div class="saving-message text-primary mt-2" id="saving-message">
                        Creating...
                        <div class="spinner-border spinner-border-sm" role="status" id="loading-spinner">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Legend Modal -->
<div class="modal fade" id="instructionsModal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="legendModalLabel">Instructions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <div>
                        <p class="p-2">
                            To create a maintenance request, click on the marker of the streetlight that needs maintenance then click "Create request" on the popup that shows. </br>
                            A user cannot post two maintenance requests on the same streetlight. <br>
                            If you have additional details about the request, fill in the "Details" field.

                         </p>
                    </div>
                </div>
                <!-- Add more legend items as needed -->
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
@endsection
<script>
   // Function to open the edit modal
   function openRequestModal(light) {
       // Populate the edit modal fields with the streetlight's current data
       document.getElementById('streetlight-id').value = light.id;

       if (light.status !== 'working') {

        const submitButton = document.getElementById('submitButton');
        submitButton.style.display = 'none';

        // Disable the input fields
        const inputFields = document.querySelectorAll('textarea');
        inputFields.forEach(field => {
            field.disabled = true; // Disable the input field
        });
        
       }

       var editModal = new bootstrap.Modal(document.getElementById('requestModal'));
       editModal.show();
   }
</script>


