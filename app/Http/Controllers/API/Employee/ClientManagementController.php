<?php
namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Notifications\Client\ClientApprovedNotification;
use App\Notifications\Client\ClientRejectedNotification;
use App\Notifications\Client\ClientSuspendedNotification;
use App\Http\Requests\Employee\Client\UpdateClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ClientManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%");
            });
        }

        $clients = $query->orderBy('created_at', 'desc')->get();
        
        $clients = $clients->map(function ($client) {
            $clientArray = $client->toArray();
            if ($client->photo) {
                $clientArray['image'] = asset('storage/' . $client->photo);
                $clientArray['photo'] = asset('storage/' . $client->photo);
                $clientArray['photo_path'] = $client->photo;
            }
            return $clientArray;
        });
        
        return response()->json($clients);
    }

    // List clients who are still pending approval
    public function pendingVerified()
    {
        $clients = Client::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $clients = $clients->map(function ($client) {
            $clientArray = $client->toArray();
            if ($client->photo) {
                $clientArray['image'] = asset('storage/' . $client->photo);
                $clientArray['photo'] = asset('storage/' . $client->photo);
                $clientArray['photo_path'] = $client->photo;
            }
            return $clientArray;
        });

        return response()->json($clients);
    }

    public function approved()
    {
        $clients = Client::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $clients = $clients->map(function ($client) {
            $clientArray = $client->toArray();
            if ($client->photo) {
                $clientArray['image'] = asset('storage/' . $client->photo);
                $clientArray['photo'] = asset('storage/' . $client->photo);
                $clientArray['photo_path'] = $client->photo;
            }
            return $clientArray;
        });
        
        return response()->json($clients);
    }

// List suspended clients
    public function suspended()
    {
        $clients = Client::where('status', 'suspended')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $clients = $clients->map(function ($client) {
            $clientArray = $client->toArray();
            if ($client->photo) {
                $clientArray['image'] = asset('storage/' . $client->photo);
                $clientArray['photo'] = asset('storage/' . $client->photo);
                $clientArray['photo_path'] = $client->photo;
            }
            return $clientArray;
        });
        
        return response()->json($clients);
    }

    // List rejected clients
    public function rejected()
    {
        $clients = Client::where('status', 'rejected')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $clients = $clients->map(function ($client) {
            $clientArray = $client->toArray();
            if ($client->photo) {
                $clientArray['image'] = asset('storage/' . $client->photo);
                $clientArray['photo'] = asset('storage/' . $client->photo);
                $clientArray['photo_path'] = $client->photo;
            }
            return $clientArray;
        });
        
        return response()->json($clients);
    }

    // Show client details
    public function show($id)
    {
        $client = Client::findOrFail($id);
        $clientArray = $client->toArray();
        
        if ($client->photo) {
            $clientArray['image'] = asset('storage/' . $client->photo);
            $clientArray['photo'] = asset('storage/' . $client->photo);
            $clientArray['photo_path'] = $client->photo;
        }
        
        return response()->json($clientArray);
    }

    // Store (create) new client
    public function store(Request $request)
    {
        $validationRules = [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:clients,email',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'required|string|min:6',
        ];

        // Only validate photo if it's provided
        if ($request->hasFile('photo')) {
            $validationRules['photo'] = 'required|image|mimes:jpeg,png,jpg,gif|max:10240';
        }

        $data = $request->validate($validationRules);

        $data['password'] = Hash::make($data['password']);
        $data['status'] = 'active'; // Auto-approve clients created by employees
        $data['email_verified_at'] = now(); // تحقق تلقائي من البريد

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('clients/photos', 'public');
        }

        $client = Client::create($data);
        $client->refresh();
        $clientArray = $client->toArray();
        
        if ($client->photo) {
            $clientArray['image'] = asset('storage/' . $client->photo);
            $clientArray['photo'] = asset('storage/' . $client->photo);
            $clientArray['photo_path'] = $client->photo;
        }

        return response()->json([
            'message' => 'Client created successfully',
            'client'  => $clientArray,
        ], 201);
    }

    // Update client information (same pattern as Admin EmployeeController)
    public function update(UpdateClientRequest $request, $id)
    {
        $client = Client::findOrFail($id);

        $data = $request->validated();

        // Handle photo upload: store file and set path (never pass UploadedFile to update)
        if ($request->hasFile('photo')) {
            if ($client->photo) {
                Storage::disk('public')->delete($client->photo);
            }
            $data['photo'] = $request->file('photo')->store('clients/photos', 'public');
        } else {
            unset($data['photo']);
        }

        $client->update($data);
        $client->refresh();
        $clientArray = $client->toArray();

        if ($client->photo) {
            $clientArray['image'] = asset('storage/' . $client->photo);
            $clientArray['photo'] = asset('storage/' . $client->photo);
            $clientArray['photo_path'] = $client->photo;
        }

        return response()->json([
            'message' => 'Client profile updated successfully',
            'client'  => $clientArray,
        ]);
    }

    // Approve (activate) client account
    public function activate($id)
    {
        $client = Client::findOrFail($id);

        // تأكد من تعيين email_verified_at إذا لم يكن موجوداً
        if (! $client->email_verified_at) {
            $client->email_verified_at = now();
        }

        $client->status = 'active';
        $client->save();

        $client->notify(new ClientApprovedNotification($client));

        return response()->json(['message' => 'Client approved successfully and notified.']);
    }

    // Reject a client account
    public function reject($id)
    {
        $client = Client::findOrFail($id);

        // Allow rejection only if status is still pending
        if ($client->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending clients can be rejected.',
            ], 400);
        }

        $client->status = 'rejected';
        $client->save();

        $client->notify(new ClientRejectedNotification($client));

        return response()->json([
            'message' => 'Client rejected successfully and notified.',
        ]);
    }
    // Suspend a client account
    public function suspend($id)
    {
        $client         = Client::findOrFail($id);
        $client->status = 'suspended';
        $client->save();

        $client->notify(new ClientSuspendedNotification($client));

        return response()->json(['message' => 'Client suspended successfully and notified.']);
    }

    // Archive (soft delete) a client
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json(['message' => 'Client archived successfully.']);
    }

    // List archived (soft deleted) clients
    public function archived()
    {
        $clients = Client::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();
        
        $clients = $clients->map(function ($client) {
            $clientArray = $client->toArray();
            if ($client->photo) {
                $clientArray['image'] = asset('storage/' . $client->photo);
                $clientArray['photo'] = asset('storage/' . $client->photo);
                $clientArray['photo_path'] = $client->photo;
            }
            return $clientArray;
        });
        
        return response()->json($clients);
    }

    // Restore archived client
    public function restore($id)
    {
        $client = Client::onlyTrashed()->findOrFail($id);
        $client->restore();

        return response()->json(['message' => 'Client restored successfully.']);
    }

    // Permanently delete archived client
    public function forceDelete($id)
    {
        $client = Client::onlyTrashed()->findOrFail($id);
        $client->forceDelete();

        return response()->json(['message' => 'Client permanently deleted.']);
    }
}
