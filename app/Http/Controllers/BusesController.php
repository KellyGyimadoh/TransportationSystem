<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBusesRequest;
use App\Http\Requests\UpdateBusesRequest;
use App\Models\Buses;

class BusesController extends Controller
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
    public function store(StoreBusesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Buses $buses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Buses $buses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusesRequest $request, Buses $buses)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buses $buses)
    {
        //
    }
}
