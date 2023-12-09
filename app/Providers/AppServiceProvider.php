<?php

namespace App\Providers;

use App\Http\Controllers\Interfaces\Dashboard\OptionServiceInterface as DashboardOptionServiceInterface;
use App\Http\Controllers\Interfaces\Dashboard\StructureDescServiceInterface;
use App\Http\Controllers\Interfaces\Dashboard\StructureServiceInterface;
use App\Http\Controllers\Interfaces\Dashboard\UserServiceInterface;
use App\Http\Controllers\Interfaces\Front\OptionServiceInterface;
use App\Http\Controllers\Interfaces\Front\StructureDescServiceInterface as FrontStructureDescServiceInterface;
use App\Http\Controllers\Interfaces\Front\StructureServiceInterface as FrontStructureServiceInterface;
use App\Http\Controllers\Interfaces\Front\WellDataServiceInterface;
use App\Http\Controllers\Services\Dashboard\OptionService as DashboardOptionService;
use App\Http\Controllers\Services\Dashboard\StructureDescService;
use App\Http\Controllers\Services\Dashboard\StructureService;
use App\Http\Controllers\Services\Dashboard\UserService;
use App\Http\Controllers\Services\Front\OptionService;
use App\Http\Controllers\Services\Front\StructureDescService as FrontStructureDescService;
use App\Http\Controllers\Services\Front\StructureService as FrontStructureService;
use App\Http\Controllers\Services\Front\WellDataService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class,UserService::class);
        $this->app->bind(DashboardOptionServiceInterface::class,DashboardOptionService::class);
        $this->app->bind(StructureServiceInterface::class,StructureService::class);
        $this->app->bind(StructureDescServiceInterface::class,StructureDescService::class);
        $this->app->bind(OptionServiceInterface::class,OptionService::class);
        $this->app->bind(FrontStructureServiceInterface::class, FrontStructureService::class);
        $this->app->bind(FrontStructureDescServiceInterface::class,FrontStructureDescService::class);
        $this->app->bind(WellDataServiceInterface::class,WellDataService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
    }
}
