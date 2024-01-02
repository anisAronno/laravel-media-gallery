<?php

namespace AnisAronno\MediaGallery\Traits;

use AnisAronno\MediaGallery\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasOwnedMedia
{
    /**
     * Get all media owned by this user/team.
     */
    public function ownedMedia(): MorphMany
    {
        return $this->morphMany(Media::class, 'owner', 'owner_type', 'owner_id');
    }
}
