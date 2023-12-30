<?php

namespace AnisAronno\MediaGallery\Observers;

use AnisAronno\MediaGallery\Helpers\CacheKey;
use AnisAronno\MediaGallery\Models\Image;
use Illuminate\Support\Facades\Cache;

class ImageObserver
{
    protected $mediaGalleryCacheKey = '';

    public function __construct()
    {
        $this->mediaGalleryCacheKey = CacheKey::getMediaGalleryCacheKey();
    }

    /**
     * Handle the Image "created" event.
     *
     * @param  Image  $image
     * @return void
     */
    public function created(Image $image)
    {
        $this->clearCache();
    }

    /**
     * Handle the Image "updated" event.
     *
     * @param  Image  $image
     * @return void
     */
    public function updated(Image $image)
    {
        $this->clearCache();
    }

    /**
     * Handle the Image "deleted" event.
     *
     * @param  Image  $image
     * @return void
     */
    public function deleted(Image $image)
    {
        $this->clearCache();
    }

    /**
     * Handle the Image "restored" event.
     *
     * @param  Image  $image
     * @return void
     */
    public function restored(Image $image)
    {
        $this->clearCache();
    }

    /**
     * Handle the Image "force deleted" event.
     *
     * @param  Image  $image
     * @return void
     */
    public function forceDeleted(Image $image)
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
