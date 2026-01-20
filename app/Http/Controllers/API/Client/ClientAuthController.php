<?php
namespace App\Http\Controllers\API\Client;

use App\Models\Client;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Client\ClientLoginRequest;
use App\Http\Requests\Client\ClientRegisterRequest;
use App\Notifications\Employee\NewClientRegistrationNotification;

class ClientAuthController extends Controller
{
    public function register(ClientRegisterRequest $request)
    {
        $data = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('clients/photos', 'public');
        }

        $client = Client::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
            'phone'    => $data['phone'] ?? null,
            'address'  => $data['address'] ?? null,
            'photo'    => $data['photo'] ?? null,
            'status'   => 'pending',
            'email_verified_at' => now(), // تحقق تلقائي من البريد
        ]);

        $employees = Employee::all();
        foreach ($employees as $employee) {
            $employee->notify(new NewClientRegistrationNotification($client));
        }

        return response()->json([
            'message' => 'Registered successfully. Your account is pending approval.',
        ], 201);

    }

    public function login(ClientLoginRequest $request)
    {
        $client = Client::where('email', $request->email)->first();

        if (! $client || ! Hash::check($request->password, $client->password)) {
            throw ValidationException::withMessages([
                'email' => ['The credentials are incorrect.'],
            ]);
        }

        if ($client->status !== 'active') {
            return response()->json([
                'message'   => match ($client->status) {
                    'pending'   => 'Your account is awaiting approval.',
                    'suspended' => 'Your account is suspended.',
                    default     => 'Your account is inactive.'
                },
            ], 403);
        }

        $token = $client->createToken('client-token')->plainTextToken;

        return response()->json([
            'token'  => $token,
            'client' => [
                'id'      => $client->id,
                'name'    => $client->name,
                'email'   => $client->email,
                'phone'   => $client->phone,
                'address' => $client->address,
                'photo'   => $client->photo,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }
}
