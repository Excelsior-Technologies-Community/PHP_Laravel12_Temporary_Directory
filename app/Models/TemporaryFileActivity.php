<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryFileActivity extends Model
{
    protected $fillable = [
        'file_name',
        'file_type',
        'operation',
        'file_size',
        'status',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];
}