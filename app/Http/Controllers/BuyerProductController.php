<?php

namespace App\Http\Controllers;

use App\Data\ProductData;
use App\Models\Buyer;
use App\Models\Product;
use Illuminate\Http\Request;

class BuyerProductController extends apiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buyers = Buyer::with('transactions.product')->get();

        $products = $buyers->flatMap(function ($buyer) {

            return $buyer->transactions->pluck('product');

        })->unique('id')->values();
        
        return $this->showAll($products,200,ProductData::class);
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
    public function show($id)
    {
        $pro = Buyer::find($id);
        if(!$pro)
        {
            return $this->errorResponse('Buyer not found', 404);
        }
        $products = $pro->transactions->pluck('product');
        return response()->json($products);
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
