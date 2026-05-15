<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Buyer;
use Illuminate\Http\Request;

class ProductBuyerController extends apiController
{
    /**
     * Display a listing of the resource.
     */
   public function index(Product $product)
    {
  
    // $transactions = Product::whereHas('transactions')->get();
       $buy = Buyer::whereHas('transactions.product')->get();
    // $buyers = $transactions->map(function($transaction) {
        // return $transaction->pluck('buyer');
    // })->unique('id')->values();
 
    // $buyers = $product->transactions()->with('buyer')->get();

    // $buyers = Transactions::whereHas('buyer')->get();

    return response()->json($buy);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
