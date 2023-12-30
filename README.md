# Laravel Media Gallery

## Table of Contents

-   [Laravel Media Gallery](#laravel-media-gallery)
    -   [Table of Contents](#table-of-contents)
    -   [Introduction](#introduction)
    -   [Installation](#installation)
    -   [Publish Migration and Config](#publish-migration-and-config)
        -   [Publish Migration, Config](#publish-migration-config)
    -   [Uses](#uses)
        -   [Eloquent Factories Relation Mapping](#eloquent-factories-relation-mapping)
        -   [Retrieve media by owner](#retrieve-media-by-owner)
    -   [API Route for Media/Image](#api-route-for-mediaimage)
    -   [Authentication](#authentication)
    -   [Use Media with Relational Model](#use-media-with-relational-model)
    -   [Working with Single or Featured Image](#working-with-single-or-featured-image)
    -   [Helper Methods](#helper-methods)
    -   [Fetch Media/Image from Relational Model](#fetch-mediaimage-from-relational-model)
    -   [Contribution Guide](#contribution-guide)
    -   [License](#license)

## Introduction

The Laravel Media Gallery simplifies media and image file management in your Laravel project. This README provides installation instructions, usage examples, and additional information.

## Installation

To get started, install the package using Composer:

```shell
composer require anisaronno/laravel-media-gallery
```

## Publish Migration and Config

For media library features, follow these steps:

### Publish Migration, Config

Publish the migration, factory and seeder file:

```shell
php artisan vendor:publish --tag=gallery-migration
```

Publish the Config file:

```shell
php artisan vendor:publish --tag=gallery
```

Run Migration

Apply the migrations to set up the media storage table:

```shell
php artisan migrate
```

## Uses

To use media storage in any model (e.g., User Blog, Product), add the `HasMedia` trait:

```php
use AnisAronno\MediaGallery\Traits\HasMedia;
use HasMedia;
```

### Eloquent Factories Relation Mapping

For setting up seed data with relation mapping (e.g., User has Blog, Blog uses HasMedia Trait), use the following code in a seeder:

```php
use App\Models\User;
use AnisAronno\MediaGallery\Database\Factories\ImageFactory;

User::factory(20)
    ->has(ImageFactory::new()->count(5), 'images')
    ->afterCreating(function ($blog)
    {
        $blog->images->first()->pivot->is_featured = 1;
        $blog->images->first()->pivot->save();
    })
    ->create();
```

### Retrieve media by owner

To retrieve media by the user, use the `HasMedia` trait on the User/Team/Admin or any other model authorized to upload media:

```php
use AnisAronno\MediaGallery\Traits\HasMedia;
use HasMedia;

$user = User::find(1); // or auth()->user();
$user->ownedImages();
```

## API Route for Media/Image

To manage your media storage, you can use the following routes:

-   Get all images: `api/image` (GET)
-   Get a single image: `api/image/{id}` (GET)
-   Store an image: `api/image` (POST)
-   Delete an image: `api/image/{id}` (DELETE)
-   Update an image: `api/image/update` (POST)
-   Delete all images: `image/delete-all` (POST)

## Authentication

You can customize the authentication guard for the routes by publishing the config file and changing the 'guard' key to your desired authentication guard:

```
'guard' => ['auth'],
```

## Use Media with Relational Model

For storing images for a relational model (e.g., Blog), use the following methods:

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
