<?php

namespace App\Providers;

use App\Contracts\EventManagementServiceInterface;
use App\Contracts\EventsRepositoryInterface;
use App\Contracts\EventTicketsRepositoryInterface;
use App\Contracts\RegistrationServiceInterface;
use App\Contracts\UserEventRegistrationRepositoryInterface;
use App\Repositories\EventsRepository;
use App\Repositories\EventTicketsRepository;
use App\Repositories\UserEventRegistrationRepository;
use App\Service\EventManagementService;
use App\Service\RegistrationService;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(EventsRepositoryInterface::class, EventsRepository::class);
        $this->app->bind(EventTicketsRepositoryInterface::class, EventTicketsRepository::class);
        $this->app->bind(UserEventRegistrationRepositoryInterface::class, UserEventRegistrationRepository::class);

        $this->app->bind(EventManagementServiceInterface::class, EventManagementService::class);
        $this->app->bind(RegistrationServiceInterface::class, RegistrationService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
