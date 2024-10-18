<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LandUse;
use App\Models\Streetlight;
use App\Models\Population;
use App\Models\Building;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use Illuminate\Support\Facades\Http;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        set_time_limit(300);
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->getRealPath(); // Get the uploaded file path
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0); // Skip the header row

            $newStreetlightIds = [];

            foreach ($csv as $row) {
                if (!isset($row['Longitude']) && !isset($row['name']) && !isset($row['Latitude'])) {

                    return redirect()->back()->with('error', 'Error importing, there is no streetlights data in this file!');
                }
                 $streetlight = Streetlight::create([
                    'name' => $row['name'],
                    'status' => isset($row['status']) ? $row['status'] : 'working',
                    'description' => isset($row['description']) ? $row['description'] : '',
                    'crime_level' => isset($row['crime_level']) ? $row['crime_level'] : 'low',
                    'energy_source' => isset($row['energy_source']) ? $row['energy_source'] : 'grid',
                    'location' => DB::raw("ST_GeomFromText('POINT({$row['Longitude']} {$row['Latitude']})', 4326)"),
                ]);

                $newStreetlightIds[] = $streetlight->id;

            }
               // ADD WARDS TO STREETLIGHTS
                $geojsonPath = public_path('geojson/chitungwiza.geojson');
                $geojson = json_decode(file_get_contents($geojsonPath), true);
    
            // Loop through each feature in the GeoJSON
            foreach ($geojson['features'] as $feature) {
                $wardName = $feature['properties']['ADM3_EN']; // Adjust based on your GeoJSON properties
                $polygon = $feature['geometry'];
    
                // Update streetlights within the current polygon
                DB::table('streetlights')
                    ->whereIn('id', $newStreetlightIds)
                    ->whereRaw('ST_Within(location, ST_GeomFromGeoJSON(?))', [json_encode($polygon)])
                    ->update(['ward' => $wardName]); // Update the ward field

            }

          return redirect()->back()->with('success', 'Streetlights data imported successfully!');


    }

}
