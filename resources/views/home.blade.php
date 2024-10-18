@extends('layouts.real')

@section('content')
    <!-- Background Image -->
    <div id="background-image"></div>

    <!-- Foreground Content (Responsive Boxes) -->
    <div class="overlay-content">
        <div class="container">
            <div class="row justify-content-center align-items-center vh-100">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h1 class="card-title">{{\App\Models\Streetlight::count()}}</h1>
                            <p class="card-text">All streetlights</p>
                            @if(Auth::user()->role == 'technician')
                            <a href="{{route('streetlights')}}" class="btn btn-primary">Monitor streetlight activities</a>
                            @else
                            <a href="{{route('requests')}}" class="btn btn-primary">Create request</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h1 class="card-title">{{\App\Models\Streetlight::where('status', '!=', 'working')->count()}}</h1>
                            <p class="card-text">Open requests</p>
                            <a href="{{route('requests')}}" class="btn btn-primary">Create request</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h1 class="card-title">{{\App\Models\User::where('role', '=', 'technician')->count()}}</h1>
                            <p class="card-text">Technicians available</p>
                            @if(Auth::user()->role == 'admin')
                            <a href="{{route('users.index')}}" class="btn btn-primary">Manage users</a>
                            @else
                            <a href="{{route('requests')}}" class="btn btn-primary">Create request</a>
                            @endif
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    <style>
        /* Fullscreen background image */
        #background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('{{ asset('images/map.png') }}') no-repeat center center fixed;
            background-size: cover;
            z-index: -1; /* Ensure image stays behind the content */
        }

        /* Foreground content */
        .overlay-content {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 999; /* Bring the content above the background */
            padding-top: 100px; /* Adjust to match the height of the navbar */
        }

        /* Adjust navbar z-index to ensure it's above background */
        .navbar {
            z-index: 1000;
        }

        /* Responsive styling for the cards */
        .card {
            text-align: center;
            padding: 20px;
        }

        .card-title {
            font-size: 3rem;
        }

        .card-text {
            margin-bottom: 10px;
        }

        /* Adjustments to ensure full viewport height for proper centering */
        .vh-100 {
            min-height: calc(100vh - 100px); /* Subtract navbar height */
        }
    </style>



