<?php

namespace AnisAronno\MediaGallery;

use AnisAronno\MediaGallery\Models\Media;
use AnisAronno\MediaGallery\Observers\MediaObserver;
use Illuminate\Support\ServiceProvider;

class MediaGalleryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->registerMigration();
        $this->registerConfig();
        Media::observe(MediaObserver::class);
    }

    protected function registerMigration()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations/2023_01_06_195610_create_media_table.php'      => database_path('migrations/'.date('Y_m_d_His', time()).'_create_media_table.php'),
                __DIR__.'/../database/migrations/2023_02_11_174512_create_mediables_table.php'  => database_path('migrations/'.date('Y_m_d_His', time() + 60).'_create_mediables_table.php'),
                __DIR__.'/../database/factories/MediaFactory.php'                               => database_path('factories/MediaFactory.php'),
                __DIR__.'/../database/seeder/MediaSeeder.php'                                   => database_path('seeders/MediaSeeder.php'),
            ], 'media-migration');
        }
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
           __DIR__.'/Config/media.php' => config_path('media.php'),
        ], 'media');

        $this->mergeConfigFrom(
            __DIR__.'/Config/media.php',
            'media'
        );
    }
}
