<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Data\BuyerData;

class BuyerSellerController extends apiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Buyer $buyer, Product $product)
    {
        // $buyers = $buyer->transactions()->with('product.seller')->get()->pluck('product.seller')->unique('id')->values();
        // $buyer = Transactions::with('buyer')->get();
        // $pro = Product::with('seller')->get()->pluck('seller')->unique('id')->values();
        // return $this->showAll($pro);
        
        $buyers = Buyer::with('transactions.product.seller')->get();

        $buyersWithSellers = $buyers->map(function ($buyer) {
        // Get unique sellers from the buyer's transactions
        $sellers = $buyer->transactions
            ->pluck('product.seller') // Extract the seller from each transaction's product
            ->unique('id') // Ensure sellers are unique by ID
            ->values(); // Reset the collection keys

        return [
            'buyer' => $buyer, // Include buyer details
            'sellers' => $sellers // Include unique sellers
        ];
    });
       $sell = $buyersWithSellers->pluck('sellers')->unique('id')->values()->flatten();

        return $this->showAll($sell,200,BuyerData::class);
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
    public function show(Buyer $buyer)
    {
        
        $buyers = $buyer->with('transactions.product.seller')->get(); 
        $buyerWithSeller = $buyers->map(function ($buyers) {
            $seller = $buyers->transactions->pluck('product.seller')
            ->unique('id')
             ->values();

             return [
                'buyer' => $buyers,
                'seller' => $seller
             ];
            });
            $sellers = $buyerWithSeller->pluck('seller')->unique('id')->values()->flatten();

        return response()->json($sellers);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Buyer $buyer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Buyer $buyer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buyer $buyer)
    {
        //
    }
}
