<?php

namespace App\Http\Controllers\API\Client;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Client\ClientLoginRequest;
use App\Http\Requests\Client\ClientRegisterRequest;

class ClientAuthController extends Controller
{
     public function register(ClientRegisterRequest $request)
    {
        $data = $request->validated();

        $client = Client::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
            'status'   => 'pending',
        ]);

        event(new Registered($client)); // Laravel will send email verification

        return response()->json([
            'message' => 'Registered successfully. Check your email for verification.'
        ], 201);
    }

    public function login(ClientLoginRequest $request)
    {
        $client = Client::where('email', $request->email)->first();

        if (! $client || ! Hash::check($request->password, $client->password)) {
            throw ValidationException::withMessages([
                'email' => ['The credentials are incorrect.']
            ]);
        }

        if (! $client->hasVerifiedEmail()) {
            return response()->json(['message' => 'Please verify your email address.'], 403);
        }

        if ($client->status !== 'active') {
            return response()->json([
                'message' => match($client->status) {
                    'pending' => 'Your account is awaiting approval.',
                    'suspended' => 'Your account is suspended.',
                    default => 'Your account is inactive.'
                }
            ], 403);
        }

        $token = $client->createToken('client-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'client' => [
                'id'    => $client->id,
                'name'  => $client->name,
                'email' => $client->email,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }
}
