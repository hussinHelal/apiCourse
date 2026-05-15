<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\apiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginController extends apiController
{
    use AuthenticatesUsers;
    
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    protected $redirectTo = '/home';

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'email is required ',
            'email.email'       => 'wrong email ',
            'password.required' => 'password is required',
        ]);
    }

    
    protected function sendFailedLoginResponse(Request $request)
    {
        $email      = $request->input('email');
        $userExists = User::where('email', $email)->exists();
        $pass       = $request->input('password');
        $passExists = User::where('password', $pass)->exists();

        if (!$userExists) {
           
            $message = 'wrong email';
            $field   = 'email';
        } elseif (!$passExists) {
           
            $message = 'wrong password';
            $field   = 'password';
        } else {
            $message = 'wrong email and password';
            $field  = 'email';
        }

       
        if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors'  => [
                    $field => [$message]
                ]
            ], 422);
        }

       
        throw ValidationException::withMessages([
            $field => [$message]
        ]);
    }

  

  
}
