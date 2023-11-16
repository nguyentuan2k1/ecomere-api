<?php

namespace App\Providers;

use App\Repositories\PasswordReset\PasswordResetInterface;
use App\Repositories\PasswordReset\PasswordResetRepository;
use App\Repositories\User\UserInterface;
use App\Repositories\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(PasswordResetInterface::class, PasswordResetRepository::class);
    }

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
