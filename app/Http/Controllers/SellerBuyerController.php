<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\Buyer;
use Illuminate\Http\Request;
use App\Data\SellerData;
use App\Data\BuyerData;
class SellerBuyerController extends apiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $buyer = $seller->product()->whereHas('transactions')->with('transactions.buyer')->get()
        // ->pluck('transactions')->collapse()->pluck('buyer')->unique('id')->values();

        $buyer = Buyer::whereHas('transactions.product.seller')->distinct()->get();


        return $this->showAll($buyer,200,SellerData::class);
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
    public function show(Seller $seller,$id)
    {
        $seller_id = $seller->find($id);
        // dd($seller_id); 
        $sellers = $seller_id->whereHas('product.transactions.buyer')
        // ->with('product.seller')
        ->get();
        // ->pluck('transactions')
        // ->collapse()
        // ->pluck('seller')
        // ->unique('id')
        // ->values();
    //    dd($sellers);
    return $this->showAll($sellers,200,sellerData::class);
    // return response()->json($sellers);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Seller $seller)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Seller $seller)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seller $seller)
    {
        //
    }
}
