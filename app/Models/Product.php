<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_produk',
        'name',
        'description',
        'unit',
        'stock',
        'purchase_price',
        'selling_price'
    ];

    //relasi tabel
}
