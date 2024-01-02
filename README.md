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
        -   [Authentication and Configuration](#authentication-and-configuration)
            -   [Customizing Authentication Guard:](#customizing-authentication-guard)
            -   [Restricting Media Viewing:](#restricting-media-viewing)
            -   [Defining Gate for Managing Media:](#defining-gate-for-managing-media)
            -   [Cache Expiry Time:](#cache-expiry-time)
    -   [Use Media with Relational Model](#use-media-with-relational-model)
    -   [Working with Single or Featured Media](#working-with-single-or-featured-media)
    -   [Helper Methods](#helper-methods)
    -   [API Route for Media/Media](#api-route-for-mediamedia)
    -   [Fetch Media/Media from Relational Model](#fetch-mediamedia-from-relational-model)
    -   [Contribution Guide](#contribution-guide)
    -   [License](#license)

## Introduction

The Laravel Media Gallery simplifies media and media file management in your Laravel project. This README provides installation instructions, usage examples, and additional information.

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
php artisan vendor:publish --tag=media-migration
```

Publish the Config file:

```shell
php artisan vendor:publish --tag=media
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
use AnisAronno\MediaGallery\Database\Factories\MediaFactory;

User::factory(20)
    ->hasAttached(
        MediaFactory::new()->count(5)
    )
    ->afterCreating(function (User $user)
    {
        $featuredMedia                     = $user->media()->first();
        $featuredMedia->pivot->is_featured = true;
        $featuredMedia->pivot->save();
    })
    ->create();
```

### Retrieve media by owner

To retrieve media by the user, use the `HasMedia` trait on the User/Team/Admin or any other model authorized to upload media:

```php
use AnisAronno\MediaGallery\Traits\HasMedia;

use HasOwnedMedia;

$user = User::find(1); // or auth()->user();
$user->ownedMedia();
```

Absolutely, here's the updated text with subheadings for each section:

### Authentication and Configuration

#### Customizing Authentication Guard:

You can customize the authentication guard for the routes by [publishing the config file](#publish-migration-and-config) and changing the 'guard' key to your desired authentication guard.

```php
'guard' => ['auth'],
```

Alternatively, you can use the API middleware.

```php
'guard' => ['auth:sanctum'],
```

#### Restricting Media Viewing:

Set `view_all_media_anyone` to `false` to restrict media viewing to user-uploaded images only; default is `true`, allowing all media viewing.

```php
'view_all_media_anyone' => false,
```

#### Defining Gate for Managing Media:

Defines `canManageMediaContent` gate in `AuthServiceProvider.php` allowing designated users to manage media content.

```php
use Illuminate\Support\Facades\Gate;

Gate::define('canManageMediaContent', function (User $user) {
    return in_array($user->email, [
        'contact@anichur.com',
    ]);
});
```

#### Cache Expiry Time:

Set Cache Expiry time. Default value is 1440.

```php
'cache_expiry_time' => 1440,
```

## Use Media with Relational Model

For storing media for a relational model (e.g., Blog), use the following methods:

-   Attach: `$blog->media()->attach(array $id)`
-   Sync: `$blog->media()->sync(array $id)`
-   Delete: `$blog->media()->detach(array $id)`

## Working with Single or Featured Media

To work with a single or featured media, use the `featuredMedia` method and set `isFeatured` to `true` in the second parameter:

-   Attach: `$blog->featuredMedia()->attach(array $id, ['is_featured' => 1])`

Note: Sync and detach are the same; use `featuredMedia` instead of `featuredMedia`.

## Helper Methods

You can also use helper methods for media management:

-   For Attach: `$blog->attachMedia(array $ids, $isFeatured = false)`

-   For Sync: `$blog->syncMedia(array $ids, $isFeatured = false)`
-   For Delete: `$blog->detachMedia(array $ids, $isFeatured = false)`

## API Route for Media/Media

To manage your media storage, you can use the following routes:

-   Get all media: `api/media` (GET)
-   Get a single media: `api/media/{id}` (GET)
-   Store an media: `api/media` (POST)
-   Delete an media: `api/media/{id}` (DELETE)
-   Update an media: `api/media/update/{id}` (POST)
-   Batch Delete: `media/batch-delete` (POST)

## Fetch Media/Media from Relational Model

To retrieve media/media from a relational model:

-   Fetch all media as an array: `$blog->media`

-   Fetch the featured media only: `$blog->media`

You can access media properties like URL, title, mimes, size, and type:

-   `$media->url`
-   `$media->title`
-   `$media->mimes`
-   `$media->size`
-   `$media->type`

## Contribution Guide

Please follow our [Contribution Guide](https://github.com/anisAronno/multipurpose-admin-panel-boilerplate/blob/develop/CONTRIBUTING.md) if you'd like to contribute to this package.

## License

This package is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).
