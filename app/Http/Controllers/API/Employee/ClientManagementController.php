<?php

namespace App\Http\Controllers\API\Employee;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\ClientApprovedNotification;
use App\Notifications\ClientSuspendedNotification;

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
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($clients);
    }

    // List verified clients who are still pending approval
    public function pendingVerified()
    {
        $clients = Client::whereNotNull('email_verified_at')
                         ->where('status', 'pending')
                         ->paginate(10);

        return response()->json($clients);
    }

    // Show specific client profile
    public function show($id)
    {
        $client = Client::findOrFail($id);
        return response()->json($client);
    }

    // Update client data (name, email)
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $data = $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:clients,email,' . $client->id,
        ]);

        $client->update($data);

        return response()->json([
            'message' => 'Client profile updated successfully',
            'client'  => $client,
        ]);
    }

    // Activate/approve a client account
    public function activate($id)
    {
        $client = Client::findOrFail($id);

        if (! $client->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Client must verify email before approval.'
            ], 400);
        }

        $client->status = 'active';
        $client->save();

        // Send notification
        $client->notify(new ClientApprovedNotification());

        return response()->json([
            'message' => 'Client approved successfully and notified.'
        ]);
    }

    // Suspend a client account
    public function suspend($id)
    {
        $client = Client::findOrFail($id);
        $client->status = 'suspended';
        $client->save();

        // Send notification
        $client->notify(new ClientSuspendedNotification());

        return response()->json([
            'message' => 'Client suspended successfully and notified.'
        ]);
    }

    // Soft delete (archive) a client
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json([
            'message' => 'Client archived successfully.'
        ]);
    }

    // List archived clients
    public function archived()
    {
        $clients = Client::onlyTrashed()->paginate(10);
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
