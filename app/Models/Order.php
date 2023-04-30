<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'address',
        'phone',
        'status',
        'notes',
        'total',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot(['quantity', 'price']);
    }
}