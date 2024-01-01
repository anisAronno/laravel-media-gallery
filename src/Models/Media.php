<?php

namespace AnisAronno\MediaGallery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'mimes',
        'type',
        'size',
        'directory',
        'owner_id',
        'owner_type',
    ];

    public function mediable(): MorphTo
    {
        return $this->morphTo('mediable', '');
    }

    /**
     * Get the owner of the media (User or Team).
     */
    public function owner()
    {
        return $this->morphTo('owner', 'owner_type', 'owner_id');
    }
}
