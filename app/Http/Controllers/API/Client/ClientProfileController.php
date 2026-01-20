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
        $client = auth('client')->user();
        $clientArray = $client->toArray();
        
        if ($client->photo) {
            $clientArray['image'] = asset('storage/' . $client->photo);
            $clientArray['photo'] = asset('storage/' . $client->photo);
            $clientArray['photo_path'] = $client->photo;
        }
        
        return response()->json($clientArray);
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
        $client->refresh();
        $clientArray = $client->toArray();
        
        if ($client->photo) {
            $clientArray['image'] = asset('storage/' . $client->photo);
            $clientArray['photo'] = asset('storage/' . $client->photo);
            $clientArray['photo_path'] = $client->photo;
        }

        return response()->json($clientArray);
    }
}
