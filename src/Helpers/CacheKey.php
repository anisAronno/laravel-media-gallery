<?php

namespace AnisAronno\MediaGallery\Helpers;

class CacheKey
{
    /**
     * Get Media CacheKey.
     * @return string
     */
    public static function getMediaGalleryCacheKey(): string
    {
        return 'media_gallery_';
    }
}
