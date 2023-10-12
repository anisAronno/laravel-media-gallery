# Laravel Media Gallery

## Table of Contents

-   [Laravel Media Gallery](#laravel-media-gallery)
    -   [Table of Contents](#table-of-contents)
    -   [Introduction](#introduction)
    -   [Installation](#installation)
    -   [Usage Guide for Laravel Media Helper](#usage-guide-for-laravel-media-helper)
    -   [Use as a Media Gallery with Storing Media in DB](#use-as-a-media-gallery-with-storing-media-in-db)
        -   [Publish Migration, Config](#publish-migration-config)
        -   [Run Migration](#run-migration)
        -   [Run Seeder](#run-seeder)
        -   [Define Relation in User Model](#define-relation-in-user-model)
        -   [Use in Another Model for Storing Media](#use-in-another-model-for-storing-media)
        -   [Use Seeder with Relation Mapping](#use-seeder-with-relation-mapping)
    -   [Media/Image Retrieve, Store, Update and Delete](#mediaimage-retrieve-store-update-and-delete)
    -   [Use Media with Relational Model](#use-media-with-relational-model)
    -   [Working with Single or Featured Image](#working-with-single-or-featured-image)
    -   [Helper Methods](#helper-methods)
    -   [Fetch Media/Image from Relational Model](#fetch-mediaimage-from-relational-model)
    -   [Contribution Guide](#contribution-guide)
    -   [License](#license)

## Introduction

The Laravel Media Gallery simplifies the management of media and image files in your Laravel project. This README provides installation instructions, usage examples, and additional information.

## Installation

To get started, install the package using Composer:

```shell
composer require anisaronno/laravel-media-gallery
```

## Usage Guide for Laravel Media Helper

For detailed usage information for media helper, please refer to the [Laravel Media Helper GitHub Repository](https://github.com/anisAronno/Laravel-Media-Helper).

## Use as a Media Gallery with Storing Media in DB

For media library features, follow these steps:

### Publish Migration, Config

Publish the migration file

```shell
php artisan vendor:publish --tag=gallery-migration
```

Publish the Config file

```shell
php artisan vendor:publish --tag=gallery
```

### Run Migration

Apply the migrations to set up the media storage table:

```shell
php artisan migrate
```

### Run Seeder

Seed the media storage table with initial data:

```shell
php artisan db:seed --class=\\AnisAronno\\MediaGallery\\Database\\Seeders\\ImageSeeder
```

### Define Relation in User Model

If you want to associate media with a user, add the following relation in your User model:

```php
public function images(): HasMany
{
    return $this->hasMany(Image::class, 'user_id', 'id');
}
```

### Use in Another Model for Storing Media

To use media storage in another model (e.g., Blog, Product), add the `HasMedia` trait:

```php
use AnisAronno\MediaGallery\Traits\HasMedia;
use HasMedia;
```

### Use Seeder with Relation Mapping

If you want to set up seed data with relation mapping (e.g., User has Blog, Blog uses HasMedia Trait), follow this code for creating a seeder:

```php
use App\Models\Blog;
use App\Models=User;
use Database\Factories\ImageFactory;

User::factory()->count(10)
    ->has(
        Blog::factory()->count(10)
        ->has(ImageFactory::new()->count(5), 'images')
        ->afterCreating(function ($blog) {
            $blog->images->first()->pivot->is_featured = 1;
            $blog->images->first()->pivot->save();
        })
    )
    ->create();
```

## Media/Image Retrieve, Store, Update and Delete

To manage your media storage, you can use the following routes:

-   Get all images: `api/image` (GET)
-   Get a single image: `api/image/{id}` (GET)
-   Store an image: `api/image` (POST)
-   Delete an image: `api/image/{id}` (DELETE)
-   Update an image: `api/image/update` (POST)
-   Delete all images: `image/delete-all` (POST)

## Use Media with Relational Model

If you want to store images for a relational model (e.g., Blog), you can use the following methods:

-   Attach: `$blog->images()->attach(array $id)`
-   Sync: `$blog->images()->sync(array $id)`
-   Delete: `$blog->images()->detach(array $id)`

## Working with Single or Featured Image

To work with a single or featured image, use the `image` method and set `isFeatured` to `true` in the second parameter:

-   Attach: `$blog->image()->attach(array $id, ['is_featured' => 1])`

Note: Sync and detach are the same; use `image` instead of `images`.

## Helper Methods

You can also use helper methods for media management:

-   For Attach: `$blog->attachImages(array $ids, $isFeatured = false)`

-   For Sync: `$blog->syncImages(array $ids, $isFeatured = false)`
-   For Delete: `$blog->detachImages(array $ids, $isFeatured = false)`

## Fetch Media/Image from Relational Model

To retrieve media/images from a relational model:

-   Fetch all images as an array: `$blog->images`
-   Fetch the featured image only: `$blog->image`

You can access image properties like URL, title, mimes, size, and type:

-   `$image->url`
-   `$image->title`
-   `$image->mimes`
-   `$image->size`
-   `$image->type`

## Contribution Guide

Please follow our [Contribution Guide](https://github.com/anisAronno/multipurpose-admin-panel-boilerplate/blob/develop/CONTRIBUTING.md) if you'd like to contribute to this package.

## License

This package is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).
