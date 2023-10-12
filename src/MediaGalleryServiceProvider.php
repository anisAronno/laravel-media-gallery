<?php

namespace AnisAronno\MediaGallery;

use AnisAronno\MediaGallery\Models\Image;
use AnisAronno\MediaGallery\Observers\ImageObserver;
use AnisAronno\MediaGallery\RouteServiceProvider;
use Illuminate\Support\ServiceProvider;

class MediaGalleryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerMigration();
        $this->registerConfig();
        Image::observe(ImageObserver::class);
    }


    protected function registerMigration()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->publishes([
            __DIR__ . '/Database/Migrations/2023_01_06_195610_create_images_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_images_table.php'),
            __DIR__ . '/Database/Migrations/2023_02_11_174512_create_imageables_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_imageables_table.php'),
        ], 'gallery-migration');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
           __DIR__.'/Config/gallery.php' => config_path('gallery.php'),
        ], 'gallery');

        $this->mergeConfigFrom(
            __DIR__.'/Config/gallery.php',
            'gallery'
        );
    }
}
