<?php

namespace AnisAronno\MediaGallery\Traits;

use AnisAronno\MediaGallery\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasOwnedImages
{
    /**
     * Get all images owned by this user/team.
     */
    public function ownedImages(): MorphMany
    {
        return $this->morphMany(Image::class, 'owner', 'owner_type', 'owner_id');
    }
 
}
