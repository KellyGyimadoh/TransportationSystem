<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripsRequest;
use App\Http\Requests\UpdateTripsRequest;
use App\Models\Trips;

class TripsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTripsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Trips $trips)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trips $trips)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTripsRequest $request, Trips $trips)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trips $trips)
    {
        //
    }
}
