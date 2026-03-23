<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'filename', 'filename_original', 'filesize', 'filesize_human',
        'hash', 'mime_type', 'carpeta', 'ruta',
    ];
}
