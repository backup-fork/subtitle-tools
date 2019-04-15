<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SubMimeRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (! $value instanceof UploadedFile) {
            return false;
        }

        $mime = file_mime($value->getRealPath());

        return $mime === 'video/mpeg' || $mime === 'image/x-tga';
    }

    public function message()
    {
        return __('validation.subidx_invalid_sub_mime');
    }
}
