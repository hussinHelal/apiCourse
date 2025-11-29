<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements OAuthenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = "users";
    const VERIFIED_USER = "1";
    const UNVERIFIED_USER = "0";

    const ADMIN_USER = "true";
    const REGULAR_USER = "false";
    protected $fillable = [
        "name",
        "email",
        "password",
        "verified",
        "verification_token",
        "admin",
    ];

    protected $hidden = ["password", "remember_token", "verification_token"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
    }

    public function isVerified()
    {
        return $this->verified == USER::VERIFIED_USER;
    }

    public function isAdmin()
    {
        return $this->admin == USER::ADMIN_USER;
    }

    public static function generateVerificationCode()
    {
        return str::random(40);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucfirst($value),
            set: fn(string $value) => strtolower($value),
        );
    }
    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucfirst($value),
            set: fn(string $value) => strtolower($value),
        );
    }
}
