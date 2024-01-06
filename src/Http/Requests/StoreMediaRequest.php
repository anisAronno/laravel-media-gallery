<?php

namespace AnisAronno\MediaGallery\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title'   => 'nullable|string|max:250|min:2',
            'caption' => 'nullable|string|max:250|min:2',
            'media'   => 'required|file|max:20480',
        ];
    }
}
