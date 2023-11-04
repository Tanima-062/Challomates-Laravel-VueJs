<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageOrTempPath implements Rule
{
    private $extensions;
    private $disk;
    private $nullable;

    /**
     * Create a new rule instance.
     *
     * @return void
     */


    public function __construct($extensions = ['png', 'jpeg', 'jpg'],  $disk = null, $nullable = false)
    {
        $this->extensions = $extensions;
        $this->disk = $disk ?: env('FILESYSTEM_DISK', 'public');
        $this->nullable = $nullable;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value instanceof UploadedFile && in_array($value->getClientOriginalExtension(), $this->extensions)) {
            return true;
        }

        if ($value && Storage::disk($this->disk)->exists($value)) {
            return true;
        }

        if(!$value && $this->nullable == true) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The image provided was invalid.';
    }
}
