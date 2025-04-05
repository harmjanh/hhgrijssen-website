<?php

namespace App\Policies;

use App\Models\AddressSubmission;
use App\Models\User;

class AddressSubmissionPolicy
{
    public function view(User $user, AddressSubmission $submission): bool
    {
        return $user->id === $submission->user_id || $user->role === 'admin';
    }
}
