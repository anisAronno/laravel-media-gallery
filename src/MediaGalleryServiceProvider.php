<?php

namespace AnisAronno\MediaGallery;

use AnisAronno\MediaGallery\Models\Image;
use AnisAronno\MediaGallery\Observers\ImageObserver;
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
        Image::observe(ImageObserver::class);
    }

    protected function registerMigration()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations/2023_01_06_195610_create_images_table.php'     => database_path('migrations/'.date('Y_m_d_His', time()).'_create_images_table.php'),
                __DIR__.'/../database/migrations/2023_02_11_174512_create_imageables_table.php' => database_path('migrations/'.date('Y_m_d_His', time() + 60).'_create_imageables_table.php'),
                __DIR__.'/../database/factories/ImageFactory.php'                               => database_path('factories/ImageFactory.php'),
                __DIR__.'/../database/seeder/ImageSeeder.php'                                   => database_path('seeders/ImageSeeder.php'),
            ], 'gallery-migration');
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
           __DIR__.'/Config/gallery.php' => config_path('gallery.php'),
        ], 'gallery');

        $this->mergeConfigFrom(
            __DIR__.'/Config/gallery.php',
            'gallery'
        );
    }
}
