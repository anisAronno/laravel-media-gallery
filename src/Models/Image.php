<?php

namespace AnisAronno\MediaGallery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
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
        'owner_type'
    ];
 
    /**
     * Get the owner of the image (User or Team).
     */
    public function owner()
    {
        return $this->morphTo('owner', 'owner_type', 'owner_id');
    }

}
