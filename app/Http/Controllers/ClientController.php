<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $clients = Client::where('user_id', Auth::id())
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('identification_number', 'like', "%{$search}%")
                      ->orWhere('contact_email', 'like', "%{$search}%");
                });
            })
            ->orderBy($request->sort_field ?? 'name', $request->sort_direction ?? 'asc')
            ->paginate(10)
            ->withQueryString();

        syncLangFiles(['clients']);

        return Inertia::render('clients/clients', [
            'clients' => $clients,
            'filters' => $request->only(['search', 'sort_field', 'sort_direction']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientRequest $request): RedirectResponse
    {
        $client = new Client($request->validated());
        $client->user_id = Auth::id();
        $client->uuid = (string) Str::uuid();
        $client->save();

        return to_route('clients.index')->with('success', 'Client created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        // Check if the client belongs to the authenticated user
        if ($client->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $client->update($request->validated());

        return to_route('clients.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        // Check if the client belongs to the authenticated user
        if ($client->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $client->delete();

        return to_route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
