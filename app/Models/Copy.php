<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Copy extends Model
{
    protected $fillable = [
        'commission_date',
        'book_id',
        'status_id',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function borrows()
    {
        return $this->belongsToMany(Borrow::class, 'borrow_copy');
    }
}