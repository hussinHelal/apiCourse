<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    /** @use HasFactory<\Database\Factories\CategoriesFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name','description'];

    protected $hidden = ['pivot','deleted_at'];
    protected $table = 'categories';
    public function products()
{
    return $this->belongsToMany(Product::class, 'category_product', 'categories_id', 'product_id');
}

}
