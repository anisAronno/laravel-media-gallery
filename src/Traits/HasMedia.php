<?php

namespace AnisAronno\MediaGallery\Traits;

use AnisAronno\MediaGallery\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasMedia
{
    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable')
        ->withPivot('is_featured')
        ->withTimestamps();
    }

    public function featuredMedia()
    {
        return $this->media()->wherePivot('is_featured', 1)->first();
    }

    /**
     * Media Attach/Store with DB.
     *
     * @param array $ids
     * @param bool $isFeatured
     * @return void
     */
    public function attachMedia(array $ids, $isFeatured = false): void
    {
        if ($isFeatured) {
            $this->media()->attach($ids, ['is_featured' => 1]);
        } else {
            $this->media()->attach($ids);
        }
    }

    /**
     * Media Sync with DB.
     *
     * @param array $ids
     * @param bool $isFeatured
     * @return void
     */
    public function syncMedia(array $ids, $isFeatured = false): void
    {
        if ($isFeatured) {
            $this->media()->sync($ids, ['is_featured' => 1]);
        } else {
            $this->media()->sync($ids);
        }
    }

    /**
     * Detach Media with DB.
     *
     * @param array $ids
     * @return void
     */
    public function detachMedia(array $ids): void
    {
        $this->media()->detach($ids);
    }
}
