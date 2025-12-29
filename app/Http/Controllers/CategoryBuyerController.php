<?php

namespace App\Http\Controllers;

use App\Data\BuyerData;
use App\Models\Categories;
use Illuminate\Http\Request;

class CategoryBuyerController extends apiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buy = Categories::with('products.transactions.buyer')->get();
        $buyers = $buy->map(function ($stuff){
            // $buyer = $stuff->products->pluck('transactions')->flatten()->pluck('buyer')->unique()->values();
            $buyer = $stuff->products()->whereHas('transactions')->with('transactions.buyer')->get()
            ->pluck('transactions')->collapse()->pluck('buyer')->unique('id')->values();

            return [$buyer];
    });
        return $this->showAll($buyers,200,BuyerData::class);
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
    public function show(Categories $categories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categories $categories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categories $categories)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categories $categories)
    {
        //
    }
}
