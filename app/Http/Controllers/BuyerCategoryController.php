<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use Illuminate\Http\Request;
use App\Data\BuyerData;
class BuyerCategoryController extends apiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $cat = Buyer::with('transactions.product.categories')->get();

        $buyerswithcats = $cat->flatMap(function ($cats) {

        $categories = $cats->transactions->pluck('product.categories')->collapse()->unique('id')->values();

        return [
            'category' => $categories
        ];

        });

        return $this->showAll($buyerswithcats,200,BuyerData::class);
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
        $cat = Buyer::find($id);
        if(!$cat)
        {
            return $this->errorResponse('Buyer not found', 404);
        }
        $categories = $cat->categories;
        return response()->json($categories);
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
