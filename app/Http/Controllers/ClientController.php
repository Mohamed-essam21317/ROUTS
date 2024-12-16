<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // List all clients
    public function index()
    {
        $clients = Client::all();
        return response()->json($clients);
    }

    // Show a specific client
    public function show($id)
    {
        $client = Client::findOrFail($id);
        return response()->json($client);
    }

    // Create a new client
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_based_id' => 'required|string|unique:clients',
            'name' => 'required|string',
            'email' => 'required|string|email|unique:clients',
            'password' => 'required|string|min:8',
            'role' => 'required|in:Parent,Driver,Supervisor,School Admin',
            'phone_number' => 'required|string',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $client = Client::create($validated);

        return response()->json(['message' => 'Client created successfully.', 'client' => $client], 201);
    }

    // Update a client
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|string|email',
            'password' => 'sometimes|string|min:8',
        ]);

        if ($request->has('password')) {
            $validated['password'] = bcrypt($request->input('password'));
        }

        $client->update($validated);

        return response()->json(['message' => 'Client updated successfully.', 'client' => $client]);
    }

    // Delete a client
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json(['message' => 'Client deleted successfully.']);
    }
}
