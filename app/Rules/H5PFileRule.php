<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;
use ZipArchive;

class H5PFileRule implements Rule
{
    private $errorMessage = 'The file must be a valid H5P package.';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        // Check if it's an uploaded file
        if (!$value instanceof UploadedFile) {
            $this->errorMessage = 'The file must be a valid uploaded file.';
            return false;
        }

        // Check file extension
        if (strtolower($value->getClientOriginalExtension()) !== 'h5p') {
            $this->errorMessage = 'The file must have a .h5p extension.';
            return false;
        }

        // Check if file is a valid ZIP archive (H5P files are ZIP files)
        $zip = new ZipArchive();
        $result = $zip->open($value->getPathname());
        
        if ($result !== TRUE) {
            $this->errorMessage = 'The file must be a valid H5P package (ZIP archive).';
            return false;
        }

        // Check for required H5P files
        $hasH5PJson = false;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if ($filename === 'h5p.json') {
                $hasH5PJson = true;
                break;
            }
        }
        
        $zip->close();

        if (!$hasH5PJson) {
            $this->errorMessage = 'The file must be a valid H5P package containing h5p.json.';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }
}
