<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Categories;
use App\Models\Seller;
use App\Models\Transactions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    const AVAILABLE_PRODUCT = "available";
    const UNAVAILABLE_PRODUCT = "unavailable";
    protected $fillable = ['name','description','status','quantity','image','seller_id'];

    public function isAvailable()
    {
        return $this->status == Product::AVAILABLE_PRODUCT;
    }

    public function categories()
    {
        return $this->belongsToMany(Categories::class, 'category_product', 'product_id', 'categories_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transactions::class);
    }
}
