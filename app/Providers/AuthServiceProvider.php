<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Surat;
use App\Models\Approval;
use App\Policies\FilePolicy;
use App\Policies\SuratPolicy;
use App\Policies\ApprovalPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Surat::class => SuratPolicy::class,
        Approval::class => ApprovalPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('show-file', [FilePolicy::class, 'showFile']);
    }
}
