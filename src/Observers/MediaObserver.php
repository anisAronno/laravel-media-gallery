<?php

namespace AnisAronno\MediaGallery\Observers;

use AnisAronno\MediaGallery\Helpers\CacheKey;
use AnisAronno\MediaGallery\Models\Media;
use Illuminate\Support\Facades\Cache;

class MediaObserver
{
    protected $mediaGalleryCacheKey = '';

    public function __construct()
    {
        $this->mediaGalleryCacheKey = CacheKey::getMediaGalleryCacheKey();
    }

    /**
     * Handle the Media "created" event.
     *
     * @param  Media  $media
     * @return void
     */
    public function created(Media $media)
    {
        $this->clearCache();
    }

    /**
     * Handle the Media "updated" event.
     *
     * @param  Media  $media
     * @return void
     */
    public function updated(Media $media)
    {
        $this->clearCache();
    }

    /**
     * Handle the Media "deleted" event.
     *
     * @param  Media  $media
     * @return void
     */
    public function deleted(Media $media)
    {
        $this->clearCache();
    }

    /**
     * Handle the Media "restored" event.
     *
     * @param  Media  $media
     * @return void
     */
    public function restored(Media $media)
    {
        $this->clearCache();
    }

    /**
     * Handle the Media "force deleted" event.
     *
     * @param  Media  $media
     * @return void
     */
    public function forceDeleted(Media $media)
    {
        $this->clearCache();
    }

    private function clearCache()
    {
        $keys = (array) Cache::get($this->mediaGalleryCacheKey, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget($this->mediaGalleryCacheKey);
    }
}
