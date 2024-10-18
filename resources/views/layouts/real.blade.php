<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Streetlight Management') }}</title>
    <link rel="shortcut icon" href="/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link rel="stylesheet" href="{{ asset('css/streetlight-management.css') }}">
    @push('styles')
    <!-- Custom CSS if needed -->
    <style>
        body {
            padding-top: 56px; /* Ensure the content doesn't hide under the navbar */
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top px-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">Streetlight Management</a>

            <!-- Collapse Button for mobile views -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Links and Profile Dropdown -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Home</a>
                    </li>

                    @if (request()->is('request'))
                    <li class="nav-item">
                        <button class="btn btn-success nav-link" data-bs-toggle="modal" data-bs-target="#instructionsModal">Instructions</button>
                    </li>
                    @endif
                    @if (request()->is('streetlights'))
                    <li class="nav-item">
                        <button class="btn nav-link" data-bs-toggle="modal" data-bs-target="#importModal">Import Data</btn>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-success nav-link" data-bs-toggle="modal" data-bs-target="#settingsModal">Rank Lights</button>
                    </li>
                    @endif
                    
                    <!-- Profile Dropdown -->
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     this.closest('form').submit();">
                                            Logout
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

<!-- Toast Notification -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="flashToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto" id="toastTitle">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body text-bold" id="toastBody">
            <!-- Toast message content will be inserted here -->
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/streetlight-management.js') }}"></script>
    <script>
   document.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.getElementById('flashToast');
        var toast = new bootstrap.Toast(toastEl);

        @if(session('success'))
            document.getElementById('toastBody').innerText = "{{ session('success') }}";
            toast.show();
        @endif

        @if(session('error'))
            document.getElementById('toastBody').innerText = "{{ session('error') }}";
            toast.show();
        @endif

        @if($errors->any())
            // Join the error messages into a single string
            var errorMessages = '';
            @foreach($errors->all() as $error)
                errorMessages += "{{ $error }}\n"; // Append each error with a newline
            @endforeach
            document.getElementById('toastBody').innerText = errorMessages;
            toast.show();
        @endif

    });
    </script>
</body>
</html>
