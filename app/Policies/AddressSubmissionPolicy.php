<?php

namespace App\Policies;

use App\Models\AddressSubmission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressSubmissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any address submissions.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their own submissions
    }

    /**
     * Determine whether the user can view the address submission.
     */
    public function view(User $user, AddressSubmission $submission): bool
    {
        return $user->id === $submission->user_id || $user->hasRole('admin');
    }
}
