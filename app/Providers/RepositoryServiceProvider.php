<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// User
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\UserService;

// CRM Records
use App\Repositories\Interfaces\CrmRecordRepositoryInterface;
use App\Repositories\CrmRecordRepository;
use App\Services\Interfaces\CrmRecordServiceInterface;
use App\Services\CrmRecordService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // User Repository & Service bindings
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        
        // CRM Record Repository & Service bindings
        $this->app->bind(CrmRecordRepositoryInterface::class, CrmRecordRepository::class);
        $this->app->bind(CrmRecordServiceInterface::class, CrmRecordService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}