<?php

namespace App\Policies;

use App\Models\Declaration;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeclarationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any declarations.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their own declarations
    }

    /**
     * Determine whether the user can view the declaration.
     */
    public function view(User $user, Declaration $declaration): bool
    {
        return $user->id === $declaration->user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can create declarations.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create declarations
    }

    /**
     * Determine whether the user can update the declaration.
     */
    public function update(User $user, Declaration $declaration): bool
    {
        return $user->id === $declaration->user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can delete the declaration.
     */
    public function delete(User $user, Declaration $declaration): bool
    {
        return $user->id === $declaration->user_id || $user->is_admin;
    }
}
