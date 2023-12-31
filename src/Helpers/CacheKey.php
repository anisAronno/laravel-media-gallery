<?php

namespace AnisAronno\MediaGallery\Helpers;

class CacheKey
{
    /**
     * Get Image CacheKey.
     * @return string
     */
    public static function getMediaGalleryCacheKey(): string
    {
        return 'media_gallery_';
    }
}
