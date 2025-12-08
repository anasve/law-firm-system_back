<?php
namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Notifications\Client\ClientApprovedNotification;
use App\Notifications\Client\ClientRejectedNotification;
use App\Notifications\Client\ClientSuspendedNotification;
use Illuminate\Http\Request;

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

        $clients = $query->orderBy('created_at', 'desc')->get();
        return response()->json($clients);
    }

    // List email-verified clients who are still pending approval
    public function pendingVerified()
    {
        $clients = Client::whereNotNull('email_verified_at')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($clients);
    }

    public function approved()
    {
        $clients = Client::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($clients);
    }

    // List suspended clients
    public function suspended()
    {
        $clients = Client::where('status', 'suspended')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($clients);
    }

    // List rejected clients
    public function rejected()
    {
        $clients = Client::where('status', 'rejected')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($clients);
    }

    // Show client details
    public function show($id)
    {
        $client = Client::findOrFail($id);
        return response()->json($client);
    }

    // Update client information
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

    // Approve (activate) client account
    public function activate($id)
    {
        $client = Client::findOrFail($id);

        if (! $client->hasVerifiedEmail()) {
            return response()->json(['message' => 'Client must verify email before approval.'], 400);
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
