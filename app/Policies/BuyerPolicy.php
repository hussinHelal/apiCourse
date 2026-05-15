<?php

namespace App\Policies;

use App\Models\Buyer;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class BuyerPolicy
{
    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return Response::allow();
        }
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        Log::info('Policy check', [
        'user_id' => $user->id,

    ]);
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Buyer $buyer): Response
    {
       Log::info('BuyerPolicy@view called', [
        'user_id' => $user->id,
        'buyer_id' => $buyer->id,
        'buyer_user_id' => $buyer->user_id,
        'matches' => $user->id === $buyer->user_id
    ]);
        return $user->id === $buyer->user_id
        ? Response::allow()
        : Response::deny('You do not own this buyer account.');
    }

    public function purchase(User $user, Buyer $buyer): Response
    {
        return $user->id === $buyer->user_id
        ? Response::allow()
        : Response::deny('You do not own this buyer account.');
    }

    /**
     * Determine whether the user can create models.
     */
    // public function create(User $user): bool
    // {
    //     return false;
    // }

    /**
     * Determine whether the user can update the model.
     */
    // public function update(User $user, Buyer $buyer): bool
    // {
    //     return false;
    // }

    /**
     * Determine whether the user can delete the model.
     */
    // public function delete(User $user, Buyer $buyer): bool
    // {
    //     return false;
    // }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, Buyer $buyer): bool
    // {
    //     return false;
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, Buyer $buyer): bool
    // {
    //     return false;
    // }
}
