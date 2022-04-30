<?php

namespace Michaelruther95\LaravelFileHandler\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileHandlerFile extends Model
{
    use HasFactory;

    public function s3Media () {
        return $this->hasOne(\Michaelruther95\LaravelFileHandler\Models\FileHandlerS3Upload::class, 'id');
    }
}
