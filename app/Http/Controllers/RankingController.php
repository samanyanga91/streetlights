<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LandUse;
use App\Models\Streetlight;
use App\Models\Population;
use App\Models\Building;
use App\Models\ResidentFeedback;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RankingController extends Controller
{
    public function settings(Request $request){

        set_time_limit(300);

       $streetlights = Streetlight::all();

       foreach ($streetlights as $streetlight) {

        $popDensityScore = $this->calculatePopulationDensityScore($streetlight);

            // Calculate Land Use Score 
        $landUseScore = $this->calculateLandUseScore($streetlight);


            // Calculate Crime Level Score 
        $crimeLevel = $this->calculateCrimeLevelScore($streetlight);

            // Combine scores using weights (adjust as needed)
        $compositeScore = ($popDensityScore * (1/3)) + ($landUseScore * (1/3))  + ($crimeLevel * (1/3));



            // Update streetlight score in the database
        $streetlight->update(['score' => round($compositeScore, 3)]);


       }

       return redirect()->back()->with('success', 'Assignment successfully completed!');

    }

    private function calculatePopulationDensityScore($streetlight)
    {

        $geojsonPath = public_path('geojson/chitungwiza.geojson');
        $geojson = json_decode(file_get_contents($geojsonPath), true);

        foreach ($geojson['features'] as $feature) {
            $normalizedValue = $feature['properties']['Normalized'];
            $wardNumber = $feature['properties']['ADM3_EN'];

            if ($streetlight->ward == $wardNumber) {
                return $normalizedValue * 10;
            }
        }

        return 0;
    }



// Land Use Score (streetlight with 100m radius)
private function calculateLandUseScore($streetlight)
{
    // Load GeoJSON file
    $geojsonPath = public_path('geojson/landuse.geojson');


    $geojson = json_decode(file_get_contents($geojsonPath), true);


    // Loop through each feature in the GeoJSON
    foreach ($geojson['features'] as $feature) {
        $polygon = $feature['geometry'];
        $type = $feature['properties']['type']; // Type of the polygon

        // Create a 100m buffer around the streetlight's location EPSG:20935 (Arc 1950 / UTM Zone 35S),
        $isWithinBuffer = DB::table('streetlights')
        ->where('id', $streetlight->id)
        ->whereRaw('ST_Intersects(
            ST_Buffer(ST_Transform(location, 20935), 100),
            ST_Transform(ST_GeomFromGeoJSON(?), 20935)
        )', [json_encode($polygon)])
        ->exists();

        // If the buffer around the streetlight intersects with the polygon, check the type
        if ($isWithinBuffer && ($type == 'commercial' || $type == 'retail')) {
            return 10; // Score of 10 if conditions are met
        }
    }

    if ($streetlight->land_use == 'commercial') {
        return 10; // Score of 10 if conditions are met
    }
    return 0; // Return 0 if not found within any polygon or conditions are not met
}





        // Resident Feedback Score (binary)
        private function calculateCrimeLevelScore($streetlight)
        {

         return $streetlight->crime_level == 'high' ? 10 : ($streetlight->crime_level == 'moderate' ? 5 : 0);

        }

        
    



}
