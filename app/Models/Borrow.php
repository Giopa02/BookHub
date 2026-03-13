<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    protected $fillable = [
        'borrowing_date',
        'return_date',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function copies()
    {
        return $this->belongsToMany(Copy::class, 'borrow_copy');
    }
}