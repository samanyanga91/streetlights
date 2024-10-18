<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StreetlightController;
use Illuminate\Support\Facades\Cache;

Route::get('api/streetlights', [StreetlightController::class, 'index']);  // Get all streetlights
Route::get('api/population-density', [StreetlightController::class, 'population']);  // Get population data
Route::get('api/streetlights/working', [StreetlightController::class, 'working']);  // Get all streetlights
Route::get('api/streetlights/faulty', [StreetlightController::class, 'faulty']);  // Get all streetlights
Route::post('api/streetlights', [StreetlightController::class, 'store']);  // Add a new streetlight
Route::put('api/streetlights/{id}', [StreetlightController::class, 'update']);  // Update a streetlight
Route::delete('api/streetlights/{id}', [StreetlightController::class, 'destroy']);  // Delete a streetlight
Route::get('api/wards-geojson', function () {
    // Check if the GeoJSON is cached, otherwise cache it
    $geojson = Cache::rememberForever('wards_geojson', function () {
        $geojsonPath = public_path('geojson/chitungwiza.geojson');

        if (!file_exists($geojsonPath)) {
            return response()->json(['error' => 'GeoJSON file not found.'], 404);
        }

        $geojsonData = json_decode(file_get_contents($geojsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid GeoJSON format.'], 500);
        }

        return $geojsonData; // Cache the parsed GeoJSON array
    });

    // Return the cached GeoJSON as JSON response
    return response()->json($geojson);
})->name('landuse-geojson');

Route::get('api/landuse-geojson', function () {
    // Check if the GeoJSON is cached, otherwise cache it
    $geojson = Cache::rememberForever('landuse_geojson', function () {
        $geojsonPath = public_path('geojson/landuse.geojson');

        if (!file_exists($geojsonPath)) {
            return response()->json(['error' => 'GeoJSON file not found.'], 404);
        }

        $geojsonData = json_decode(file_get_contents($geojsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid GeoJSON format.'], 500);
        }

        return $geojsonData; // Cache the parsed GeoJSON array
    });

    // Return the cached GeoJSON as JSON response
    return response()->json($geojson);
})->name('wards-geojson');
