<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
<<<<<<< HEAD
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


=======
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
>>>>>>> f0bb797 (fix verification)

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)  
    {
<<<<<<< HEAD
       Log::info('Registration create method called');

=======
>>>>>>> f0bb797 (fix verification)
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'verified' => User::UNVERIFIED_USER,
            'verification_token' => User::generateVerificationCode(),
            'admin' => User::REGULAR_USER,
        ]);
<<<<<<< HEAD
<<<<<<< HEAD

        Log::info('User created successfully: ' . $user->id);
        Log::info('Verification token: ');

        return $user;
=======
         $token = User::generateVerificationCode(); 
    
=======
         $token = User::generateVerificationCode();

>>>>>>> 1d32c16 (just commit dude wth)
        $user->verification_token = $token;
        $user->save();


        return response()->json([
            'message' => 'User registered. Please check your email to verify.' ,
        ]);

>>>>>>> f0bb797 (fix verification)
    }
}
