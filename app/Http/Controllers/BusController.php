<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use Illuminate\Http\Request;

class BusController extends Controller
{
    // List all buses
    public function index()
    {
        $buses = Bus::all();
        return response()->json($buses);
    }

    // Show a specific bus
    public function show($id)
    {
        $bus = Bus::findOrFail($id);
        return response()->json($bus);
    }

    // Create a new bus
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bus_number' => 'required|string|unique:buses',
            'driver_id' => 'required|exists:clients,role_based_id',
            'supervisor_id' => 'nullable|exists:clients,role_based_id',
            'route_id' => 'required|exists:routes,id',
            'capacity' => 'required|integer',
        ]);

        $bus = Bus::create($validated);
        return response()->json(['message' => 'Bus created successfully.', 'bus' => $bus], 201);
    }

    // Update a bus
    public function update(Request $request, $id)
    {
        $bus = Bus::findOrFail($id);

        $validated = $request->validate([
            'bus_number' => 'sometimes|string',
            'driver_id' => 'sometimes|exists:clients,role_based_id',
            'supervisor_id' => 'sometimes|nullable|exists:clients,role_based_id',
            'route_id' => 'sometimes|exists:routes,id',
            'capacity' => 'sometimes|integer',
        ]);

        $bus->update($validated);

        return response()->json(['message' => 'Bus updated successfully.', 'bus' => $bus]);
    }

    // Delete a bus
    public function destroy($id)
    {
        $bus = Bus::findOrFail($id);
        $bus->delete();

        return response()->json(['message' => 'Bus deleted successfully.']);
    }

    // Update the location of a bus
    // public function updateLocation(Request $request, $id)
    // {
    //     $validated = $request->validate([
    //         'latitude' => 'required|numeric',
    //         'longitude' => 'required|numeric',
    //     ]);

    //     $bus = Bus::findOrFail($id);
    //     $bus->updateLocation($validated['latitude'], $validated['longitude']);

    //     return response()->json(['message' => 'Bus location updated successfully.']);
    // }

    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Update the bus location
        $bus = Bus::find($validated['bus_id']);
        $bus->location = json_encode([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);
        $bus->save();

        // Broadcast the location update
        event(new \App\Events\BusLocationUpdated(
            $bus->id,
            $validated['latitude'],
            $validated['longitude']
        ));

        return response()->json(['message' => 'Bus location updated successfully']);
    }
}
