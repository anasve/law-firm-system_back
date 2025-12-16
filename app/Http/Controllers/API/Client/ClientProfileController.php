<?php

namespace App\Http\Controllers\API\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Client\ClientProfileRequest;

class ClientProfileController extends Controller
{
    public function show()
    {
        return response()->json(auth('client')->user());
    }

    // Update client profile
    public function update(ClientProfileRequest $request)
    {
        $client = auth('client')->user();
        $data = $request->validated();

        // Handle password hashing
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($client->photo) {
                Storage::disk('public')->delete($client->photo);
            }
            $data['photo'] = $request->file('photo')->store('clients/photos', 'public');
        }

        $client->update($data);

        return response()->json($client);
    }
}
