<?php

namespace Rialic\StarterKit\Providers;

use Illuminate\Support\ServiceProvider;

class StarterKitServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Repository Pattern
        $this->publishes(
            [__DIR__.'/RepositoryServiceProvider.php' => app_path('Providers/RepositoryServiceProvider.php')],
            ['starter-kit', 'repository-pattern']
        );

        $this->publishes(
            [__DIR__.'/../Repository' => app_path('Repository')],
            ['starter-kit', 'repository-pattern']
        );

        $this->publishes(
            [__DIR__.'/../Exceptions/RepositoryExceptions' => app_path('Exceptions/RepositoryExceptions')],
            ['starter-kit', 'repository-pattern']
        );

        $this->publishes(
            [__DIR__.'/../Traits/HasIdWithUuids.php' => app_path('Traits/HasIdWithUuids.php')],
            ['starter-kit', 'repository-pattern']
        );

        // Service Layer
        $this->publishes(
            [__DIR__.'/../ServiceLayer' => app_path('ServiceLayer')],
            ['starter-kit', 'service-layer']
        );

        $this->publishes(
            [__DIR__.'/../Traits/HasControllerResource.php' => app_path('Traits/HasControllerResource.php')],
            ['starter-kit', 'service-layer']
        );

        $this->publishes(
            [__DIR__.'/../Traits/HasRequestResource.php' => app_path('Traits/HasRequestResource.php')],
            ['starter-kit', 'service-layer']
        );

        $this->publishes(
            [__DIR__.'/../Middleware/RequestId.php' => app_path('Http/Middleware/RequestId.php')],
            ['starter-kit', 'service-layer']
        );

        $this->publishes(
            [__DIR__.'/../Exceptions/ApiException.php' => app_path('Exceptions/ApiException.php')],
            ['starter-kit', 'service-layer']
        );

        $this->publishes(
            [__DIR__.'/../Services/Response.php' => app_path('Services/Response.php')],
            ['starter-kit', 'service-layer']
        );

        // Logging
        $this->publishes(
            [__DIR__.'/../Logging' => app_path('Logging')],
            ['starter-kit', 'logging']
        );
    }

    public function register(): void
    {

    }
}