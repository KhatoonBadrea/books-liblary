<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'review'
    ];

    //============relations

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function borrow_record()
    {
        return $this->belongsTo(Borrow_record::class);
    }
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
