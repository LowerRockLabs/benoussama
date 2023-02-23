<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'site',
        'price',
        'as',
        'traffic',
        'cuntry',
        'industry',
    ];

    protected $appends = [
        'is_on_order',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'link_id');
    }
    public function getIsOnOrderAttribute()
    {
        return $this->orders->where('user_id', auth()->id())->isEmpty();
    }
}
