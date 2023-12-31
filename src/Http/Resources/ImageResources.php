<?php

namespace AnisAronno\MediaGallery\Http\Resources;

use AnisAronno\MediaHelper\Facades\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ImageResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'url'        => Media::getURL($this->url),
            'mimes'      => $this->mimes,
            'type'       => $this->type,
            'size'       => $this->size,
            'directory'  => $this->directory,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }
}
