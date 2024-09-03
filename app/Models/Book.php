<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'author',
        'description',
        'category',
        'publiched_at',
    ];

    public function ratings()
    { 
        $this->hasMany(Rating::class);
    }


    public function borrowRecords()
    {
        return $this->hasMany(Borrow_record::class, 'book_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
