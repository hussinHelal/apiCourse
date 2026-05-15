<?php

namespace App\Http\Controllers;

use App\Data\SellerData;
use App\Models\Categories;
use App\Models\Product;
use Illuminate\Http\Request;

class CategorySellerController extends apiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Categories $category)
    {
        $cats = Categories::with("products.seller")->get();
        // $cats = $category->products->seller;

        $catwithsell = $cats->map(function ($stuff){
                $sellers = $stuff->products->pluck('seller')->unique('id')->values()->flatten();
            return [$sellers];
        });

        return $this->showAll($catwithsell,200,SellerData::class);
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
