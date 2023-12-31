<?php

namespace AnisAronno\MediaGallery\Helpers;

use AnisAronno\MediaHelper\Facades\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaDataProcessor
{
    /**
     * Media Data Processor.
     *
     * @param Request $request
     * @return array
     */
    public static function process(Request $request, $field = 'media'): array
    {
        $data       = [];
        $upload_dir = Str::plural($field);

        if ($request->$field) {
            $data['url']       = Media::upload($request, $field, $upload_dir);
            $data['directory'] = Media::getFileTypeFolder($request->$field->extension())?? '';
            $data['mimes']     = $request->$field->extension();
            $data['type']      = $request->$field->getClientMimeType();
            $data['size']      = number_format($request->$field->getSize() / (1024 * 1024), 2, '.', '').'MB';
        }

        return $data;
    }
}
