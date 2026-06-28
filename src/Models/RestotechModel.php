<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class RestotechModel extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        $factory = 'Database\\Factories\\' . class_basename(static::class) . 'Factory';

        return $factory::new();
    }
}
