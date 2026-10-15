<?php

namespace App\Providers;

use App\Models\GalleryImage;
use App\Models\SalonProfile;
use App\Models\ScheduleException;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Policies\OwnerPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Deteksi N+1 saat development (agent.md bagian 7).
        Model::preventLazyLoading($this->app->environment('local'));

        foreach ([Service::class, ServiceCategory::class, ScheduleException::class, SalonProfile::class, GalleryImage::class, User::class] as $model) {
            Gate::policy($model, OwnerPolicy::class);
        }

        \Illuminate\Support\Facades\Blade::anonymousComponentPath(resource_path('views/layouts'), 'layouts');
    }
}
