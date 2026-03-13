<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['status'];

    public function copies()
    {
        return $this->hasMany(Copy::class);
    }
}