<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User as usr ;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Data\UserData;
use Spatie\LaravelData\DataCollection;
use App\Http\Middleware\transformInput;
use App\Http\Requests\UpdateUserRequest;

class User extends apiController
{

    //   public function __construct()
    //   {
    //     parent::__construct();
    //     $this->middleware(transformInput::class)->only(['store','update']);
    //   }

      protected function getCacheTags(): array
    {
        return ['api', 'users'];
    }

    public function index()
    {
        $users = usr::all();

        return UserResource::collection( $users );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('createUser');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        LOG::info('Store method called for user creation');
         try {
            $validate = $request->validate([
                'name' => 'required',
                'email' => 'required | email | unique:users',
                'password' => 'required | min : 6 | confirmed',
            ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ', $e->errors());
            throw $e; // Re-throw to return proper error response
            }
            Log::info('Validation passed');


        $data = $validate;
        $data['password'] = bcrypt($request->password);
        $data['verified'] = Usr::UNVERIFIED_USER;
        $data['admin'] = Usr::REGULAR_USER;
        $data['verification_token'] = Usr::generateVerificationCode();
        Log::info('Generated verification token before creation');
        $user = Usr::create($data);

        Log::info('User created successfully: ' . $user->id);

        return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Usr $user)
    {
        // $usr = usr::find($id);
        $usr = UserData::from($user);

        if(!$usr){
            return $this->errorResponse(['message'=>'User Not Found','code'=>404],404);
        } else {
            // return $this->showOne($usr);
            return $usr;
        }
    }
    public function trashed()
    {
        $usr = usr::onlyTrashed();

        if(!$usr){
            return $this->errorResponse(['message'=>'User Not Found','code'=>404],404);
        } else {
            return response()->json([$usr ,'code'=> 200],200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = usr::findOrFail($id);
        // $validate = $request->validate([
        //     'email' => 'email|unique:users,email,' . $user->id ,
        //     'password' => 'min:6|confirmed',
        //     'admin' => 'in:' . Usr::ADMIN_USER . ',' . Usr::REGULAR_USER ,
        // ]);

        if ($request->has('name'))
        {
            $user->name = $request->name;
        }

        if ($request->has('email') && $request->email != $user->email)
        {
            $user->verified = Usr::UNVERIFIED_USER;
            $user->verification_token = Usr::generateVerificationCode();
            $user->email = $request->email;
        }

        if ($request->has('password'))
        {
            $user->password = bcrypt( $request->password );
        }


        if(!$user->isDirty())
        {
            return response()->json(['error'=> 'you need to send different values to update','code'=> 422],422);
        }

        if($request->has('admin'))
        {
            if(!$user->isVerified()){
                return response()->json(['Error'=> 'You are not verified to modify admin field','code'=>409],409);
            }

            $user->admin = $request->admin;
        }

        $user->save();

       return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Usr::findOrFail($id);


        $user->delete();

       return response()->json([$user ,'code'=> 200],200);
    }

    public function softDelete($id)
    {
        $user = Usr::findOrFail($id);
        $user->softDelete();

        return response()->json([$user ,'code'=> 200],200);
    }

    public function restore($id)
    {
        $user = Usr::findOrFail($id);
        $user->restore();

        return response()->json([$user ,'code'=> 200],200);
    }

    public function verify($token)
    {
        Log::info('Verification attempt  ' );
        $user = Usr::where('verification_token', $token)->firstOrFail();

        $user->verified = Usr::VERIFIED_USER;
        $user->verification_token = null;

        $user->save();

        return $this->showMessage('The account has been verified successfully', 200);

    }

    public function resendVerification(Usr $user)
    {
        $user = Usr::where('id', $user->id)->firstOrFail();

        if($user->isVerified()){
            return $this->errorResponse(['message'=>'This user is already verified','code'=>409],409);
        }

        retry(5, function () use ($user)
        {
            Mail::to($user)->queue(new TestMail($user));
        }, 200);

        return $this->showMessage('verification has been sent', 200);

    }
}
