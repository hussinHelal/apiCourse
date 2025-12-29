<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use App\Models\User;

class UserData extends Data
{
    public function __construct(

        #[MapInputName('iden')]
        #[MapOutputName("i")]
        public int $id,

        #[MapInputName("uname")]
        #[MapOutputName('n')]
        public string $name,

        #[MapOutputName('e')]
        public string $email,

        #[MapOutputName('v')]
        public string $verified,

        #[MapOutputName('a')]
        public Lazy|String|null $admin,
    ) {
    }

    public static function fromModel(User $user, bool $isAdmin = false):self
    {
        // $currentUser = auth()->user();
        // $isAdmin = $currentUser && $currentUser->admin === User::ADMIN_USER;

         return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            verified: $user->verified ? $user->verified : 'not verified',
            admin: $user->admin ? $user->admin : null,
        );
    }

}
