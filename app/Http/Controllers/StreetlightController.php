<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Streetlight;
use App\Models\Population;
use Illuminate\Support\Facades\DB;
use Auth;

class StreetlightController extends Controller
{





public function index()
{
    $streetlights = DB::select("SELECT *, ST_AsGeoJSON(location) as location FROM streetlights ORDER BY score DESC");
    return response()->json($streetlights);
}




    public function updateStreetlight(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'streetlight_id' => 'required|integer|exists:streetlights,id',
            'streetlight_name' => 'required|string|max:255',
            'streetlight_notes' => 'nullable|string|max:255',
            'streetlight_status' => 'required|string',
            'streetlight_energy_source' => 'required|string',
            'streetlight_description' => 'nullable|string',
            'streetlight_crime_level' => 'required|string',
            'streetlight_land_use' => 'nullable|string',
        ]);
        
      if (Auth::user()->role == 'admin') {
        // Find the streetlight and update it
        $streetlight = Streetlight::find($request->streetlight_id);
        $streetlight->name = $request->streetlight_name;
        $streetlight->status = $request->streetlight_status;
        $streetlight->notes = $request->streetlight_notes;
        $streetlight->description = $request->streetlight_description;
        $streetlight->energy_source = $request->streetlight_energy_source;
        $streetlight->crime_level = $request->streetlight_crime_level;
        $streetlight->land_use = $request->streetlight_land_use;
        $streetlight->save();
      }  else {
        // Find the streetlight and update it
        $streetlight = Streetlight::find($request->streetlight_id);
        $streetlight->status = $request->streetlight_status;
        $streetlight->notes = $request->streetlight_notes;
        $streetlight->save();

      } 

    
        // Redirect back to the previous page or wherever appropriate
        return redirect()->back()->with('success', 'Streetlight updated successfully!');
    }


    public function postRequest(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'streetlight_id' => 'required|integer|exists:streetlights,id',
            'details' => 'string|max:255',
        ]);
        

    
        // Find the streetlight and update it
        $streetlight = Streetlight::find($request->streetlight_id);

        if (($streetlight->ward !== auth()->user()->ward) && auth()->user()->role == 'resident') {
            return redirect()->back()->with('success', 'You cannot create a maintenance request for ths ward');
        }

        if ($streetlight->status !== 'working') {
            return redirect()->back()->with('success', 'There is an existing request for this streetlight, the request is being attended to!');
        }

        $streetlight->status = 'request maintenance';
        $streetlight->request_details = $request->details;
        $streetlight->save();

    
        // Redirect back to the previous page or wherever appropriate
        return redirect()->back()->with('success', 'Maintenace request has been created successfully!');
    }
    


    // Store a new streetlight
    public function store(Request $request)
    {
        DB::insert("INSERT INTO streetlights (name, status, installed_on, location) 
            VALUES (?, ?, ?, ST_SetSRID(ST_MakePoint(?, ?), 4326))", 
            [
                $request->input('name'),
                $request->input('status'),
                $request->input('installed_on'),
                $request->input('longitude'),
                $request->input('latitude')
            ]);
        
        return response()->json(['message' => 'Streetlight added successfully']);
    }

    // Update a streetlight
    public function update(Request $request, $id)
    {
        DB::update("UPDATE streetlights SET name = ?, status = ?, installed_on = ?, 
            location = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE id = ?", 
            [
                $request->input('name'),
                $request->input('status'),
                $request->input('installed_on'),
                $request->input('longitude'),
                $request->input('latitude'),
                $id
            ]);
        
        return response()->json(['message' => 'Streetlight updated successfully']);
    }

    // Delete a streetlight
    public function destroy($id)
    {
        DB::delete("DELETE FROM streetlights WHERE id = ?", [$id]);
        return response()->json(['message' => 'Streetlight deleted successfully']);
    }
}
