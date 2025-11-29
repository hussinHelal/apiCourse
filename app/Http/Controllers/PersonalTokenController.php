<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\Passport;

class PersonalTokenController extends Controller
{

    public function index()
    {
        $tokens = auth()->user()->tokens()->paginate(10);
         $scopes = Passport::scopes()->toArray();

         return view('personalTokens', compact('tokens', 'scopes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $token = auth()->user()->createToken($request->name);

        return redirect()->back()->with('token', $token->accessToken);
    }

    public function destroy($tokenId)
    {
        $token = auth()->user()->tokens()->find($tokenId);

        if ($token) {
            $token->revoke();
            return redirect()->back()->with('success', 'Token revoked successfully');
        }

        return redirect()->back()->with('error', 'Token not found');
    }

      public function authorizedClients()
    {
            $tokens = \Laravel\Passport\Token::where('user_id', auth()->id())
            ->with('client')
            ->whereNotNull('client_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('authorizedClients', compact('tokens'));
    }

    public function revokeClient($tokenId)
    {
         $token = auth()->user()->tokens()->with('client')->find($tokenId);

        if (!$token) {
            return redirect()->back()->with('error', 'Token not found');
        }


        if ($token->client && $token->client->personal_access_client) {
            return redirect()->back()->with('error', 'Cannot revoke personal access tokens here');
        }

        $token->revoke();


        return redirect()->back()->with('success', 'Application access revoked successfully');
    }

    public function clients()
    {
        $clients = \Laravel\Passport\Client::where('owner_id', auth()->id())
        ->where('owner_type', get_class(auth()->user()))
        ->orderBy('created_at', 'desc')
        ->get();

        return view('createClients', compact('clients'));
    }

    public function storeClient(Request $request)
    {
        $request->validate([
        'name' => 'required|string|max:255',
        'redirect' => 'required|url',
        ]);

        $plainSecret = \Illuminate\Support\Str::random(40);

        $client = new \Laravel\Passport\Client();
        $client->owner_id = auth()->id();
        $client->owner_type = get_class(auth()->user());
        $client->name = $request->name;
        $client->secret = $plainSecret;
        $client->redirect_uris = [$request->redirect];
        $client->grant_types = ['authorization_code', 'refresh_token'];
        $client->revoked = false;
        $client->forceFill(['secret' => $plainSecret])->save();

        return redirect()->back()->with('client', [
            'id' => $client->id,
            'secret' => $plainSecret
        ]);
    }

    public function updateClient(Request $request, $clientId)
    {
       $request->validate([
        'name' => 'required|string|max:255',
        'redirect' => 'required|url',
        ]);

        $client = \Laravel\Passport\Client::where('owner_id', auth()->id())
            ->where('owner_type', get_class(auth()->user()))
            ->where('id', $clientId)
            ->firstOrFail();

        $client->update([
            'name' => $request->name,
            'redirect' => [$request->redirect],
        ]);

        return redirect()->back()->with('success', 'Client updated successfully');
    }

    public function destroyClient($clientId)
    {
         $client = \Laravel\Passport\Client::where('owner_id', auth()->id())
        ->where('owner_type', get_class(auth()->user()))
        ->where('id', $clientId)
        ->firstOrFail();

         $client->delete();

        return redirect()->back()->with('success', 'Client deleted successfully');
    }

    public function regenerateSecret($clientId)
{
    $client = \Laravel\Passport\Client::where('owner_id', auth()->id())
        ->where('owner_type', get_class(auth()->user()))
        ->where('id', $clientId)
        ->firstOrFail();

    $newSecret = \Illuminate\Support\Str::random(40);

    $client->update([
        'secret' => $newSecret
    ]);

    return redirect()->back()->with('client', [
        'id' => $client->id,
        'secret' => $newSecret
    ])->with('success', 'Client secret regenerated successfully');
}
}
