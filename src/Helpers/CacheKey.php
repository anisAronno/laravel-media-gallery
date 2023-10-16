<?php

namespace AnisAronno\MediaGallery\Helpers;

class CacheKey
{
    /**
     * Get Image CacheKey
     * @return string
     */
    public static function getImageCacheKey(): string
    {
        return '_image_';
    }

}
