<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'task_id',
        'attributes',
        'result',
        'error',

    ];

    protected $casts = [
        'attributes' => 'json',
        'result' => 'boolean',
    ];
}