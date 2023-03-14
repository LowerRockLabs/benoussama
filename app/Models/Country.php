<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $incrementing = true;

    protected $primaryKey = 'id';

    protected $fillable = ['id', 'name'];

    protected $table = 'countries';

}