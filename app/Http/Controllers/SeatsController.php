<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSeatsRequest;
use App\Http\Requests\UpdateSeatsRequest;
use App\Models\Seats;

class SeatsController extends Controller
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
    public function store(StoreSeatsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Seats $seats)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Seats $seats)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSeatsRequest $request, Seats $seats)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seats $seats)
    {
        //
    }
}
