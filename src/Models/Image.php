<?php

namespace AnisAronno\MediaGallery\Models;

use AnisAronno\MediaGallery\Database\Factories\ImageFactory;
use AnisAronno\MediaHelper\Facades\Media;
use App\Models\User;
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
        'user_id',
    ];

    /**
     * Override newFactory Method for mapping model and factory
     * @return ImageFactory
     */
    protected static function newFactory()
    {
        return ImageFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getUrlAttribute($value)
    {
        return  $this->attributes['url'] = Media::getURL($value);
    }
}
