<?php

namespace App\Models;

use App\Models\Scopes\SellerScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;

#[ScopedBy([SellerScope::class])]
class Seller extends User
{
    /** @use HasFactory<\Database\Factories\SellerFactory> */
    use HasFactory;

    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
