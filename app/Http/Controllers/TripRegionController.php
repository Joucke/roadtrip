<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripRegionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Trip  $trip
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Trip $trip)
    {
        $data = $request->validate([
            'data.title' => 'bail|required|max:255',
            'data.lat' => 'required|numeric|between:-90,90',
            'data.long' => 'required|numeric|between:-180,180',
            'data.box' => 'required'
        ]);
        $trip->regions()->create($data['data']);

        return $trip->regions;
    }

    public function update(Request $request, Trip $trip, Region $region)
    {
        $data = $request->validate([
            'arrival_at' => 'date',
            'departure_at' => 'date',
        ]);
        foreach($data as $field => $value) {
            $region->$field = $value;
        }
        $region->save();
        return $trip->fresh()->regions;
    }
}
