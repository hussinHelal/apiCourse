<?php

namespace App\Policies;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SellerPolicy
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
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Seller $seller): Response
    {
        return $user->id === $seller->user_id
            ? Response::allow()
            : Response::deny('You do not own this seller.');
    }

    public function sale(User $user, User $seller): Response
    {
        return $user->id === $seller->user_id
            ? Response::allow()
            : Response::deny('You do not own this seller.');
    }

    public function editProduct(User $user, Seller $seller): Response
    {
        return $user->id === $seller->user_id
            ? Response::allow()
            : Response::deny('You do not own this seller.');
    }

    public function deleteProduct(User $user, Seller $seller): Response
    {
        return $user->id === $seller->user_id
            ? Response::allow()
            : Response::deny('You do not own this seller.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Seller $seller): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Seller $seller): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Seller $seller): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Seller $seller): bool
    {
        return false;
    }
}
