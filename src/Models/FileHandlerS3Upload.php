<?php

namespace Michaelruther95\LaravelFileHandler\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Storage;

class FileHandlerS3Upload extends Model
{
    use HasFactory;

    /**
     * Note: For adding custom attribute to a model, follow this: https://postsrc.com/code-snippets/how-to-add-custom-attribute-in-laravel-eloquent
     */

    protected $appends = ['public_path'];

    protected function getPublicPathAttribute () {
        $path = Storage::disk('s3')->url($this->path);
        return $path;
    }
}
