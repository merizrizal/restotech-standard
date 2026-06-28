<?php

namespace Restotech\Standard\Models;

class Employee extends RestotechModel
{
    protected $table = 'restotech_employees';

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
