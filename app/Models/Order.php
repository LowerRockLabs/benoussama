<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_id',
        'price',
        'user_id',
    ];


    public function link()
    {
        return $this->belongsTo(Link::class, 'link_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
