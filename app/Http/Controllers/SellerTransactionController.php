<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Data\TransactionData;
class SellerTransactionController extends apiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Seller $seller)
    {

        // $tran = Seller::whereHas('product.transactions')->get()->pluck('product')
        // ->collapse()->pluck('transactions')->collapse();

        // $tran = Seller::with('product.transactions')->get()
        // ->pluck('product')->collapse()->pluck('transactions')->collapse();

        $transactions = $seller->load('product.transactions')
        ->product->pluck('transactions')->collapse();


        return $this->showAll($transactions,200,TransactionData::class);
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
    public function show(Seller $seller)
    {
        //
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
