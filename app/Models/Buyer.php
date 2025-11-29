<?php

namespace App\Models;

use App\Models\Scopes\BuyerScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Transactions;

#[ScopedBy([BuyerScope::class])]
class Buyer extends User
{
    /** @use HasFactory<\Database\Factories\BuyerFactory> */
    use HasFactory;

    public function transactions()
    {
        return $this->hasMany(Transactions::class);
    }
}
