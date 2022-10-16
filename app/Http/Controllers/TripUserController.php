<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;

class TripUserController extends Controller
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
        $trip->users()->attach($request->only('user_id'));
        $trip->touch();

        return redirect()->to(route('trips.show', $trip));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Trip  $trip
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Trip $trip, User $user)
    {
        $trip->users()->detach($user);
        $trip->touch();

        return redirect()->to(route('trips.show', $trip));
    }
}
