<?php

namespace App\Providers;

use App\Models\Declaration;
use App\Policies\DeclarationPolicy;
use App\Models\AddressSubmission;
use App\Policies\AddressSubmissionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Declaration::class => DeclarationPolicy::class,
        AddressSubmission::class => AddressSubmissionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
