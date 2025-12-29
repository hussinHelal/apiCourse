<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    // public function creating(User $user)
    // {
    //     $user->verification_token = User::generateVerificationCode();
    //     $user->verified = User::UNVERIFIED_USER;
    //     $user->admin = User::REGULAR_USER;
    // }

    public function created(User $user):void
    {
        // retry(5, function () use ($user) {
        //     try {
        //         Mail::to($user->email)->queue(new TestMail($user));
        //     } catch (\Exception $e) {
        //         Log::error('Failed to send welcome email to user ID ' . $user->id . ': ' . $e->getMessage());
        //     }
        // }, 200);

     $this->clearCache();
    }

    public function updated(User $user):void
    {
        $this->clearCache();
    }

    public function deleted(User $user):void
    {
        $this->clearCache();
    }

    private function clearCache(): void
    {
        Cache::Tags(['api','users'])->flush();
    }
}
