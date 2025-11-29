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

}
