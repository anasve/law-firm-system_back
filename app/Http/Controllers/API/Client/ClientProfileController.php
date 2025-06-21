<?php

namespace App\Http\Controllers\API\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

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

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $client->update($data);

        return response()->json($client);
    }
}
