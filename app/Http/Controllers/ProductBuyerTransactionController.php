<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends apiController
{

   public function store(Request $request, Product $product, User $buyer)
{
    $validate = $request->validate([
        'quantity' => 'required|integer|min:1'
    ]);

    if($buyer->id == $product->seller->id){
        return $this->errorResponse("The buyer must be different from the seller", 409);
    }

    if(!$buyer->isVerified()){
        return $this->errorResponse('The buyer must be a verified user', 409);
    }

    if(!$product->seller->isVerified()){
        return $this->errorResponse('The seller must be a verified user', 409);
    }

    if(!$product->isAvailable()){
        return $this->errorResponse('The product is not available', 409);
    }

    if($product->quantity < $request->quantity){
        return $this->errorResponse('The product does not have enough units for this transaction', 409);
    }

    $transaction = DB::transaction(function () use ($request, $product, $buyer) {
        $product->quantity -= $request->quantity;
        $product->save();

        $transaction = Transactions::create([
            'quantity' => $request->quantity,
            'buyer_id' => $buyer->id,
            'product_id' => $product->id
        ]);

        return $transaction; // Return the transaction, not a response
    });

    return response()->json($transaction, 201);
}


}
