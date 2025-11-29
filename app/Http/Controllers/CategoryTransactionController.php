<?php

namespace App\Http\Controllers;

use App\Data\TransactionData;
use App\Models\Categories;
use Illuminate\Http\Request;

class CategoryTransactionController extends apiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tran = Categories::with("products.transactions")->get();
        $trans = $tran->map(function ($stuff){
            $transactions = $stuff->products->pluck('transactions')->unique('id')->values()->flatten();
            $transactions = $stuff->products()->whereHas('transactions')->with('transactions')->get()->pluck('transactions')->collapse();
            return [$transactions];
        });
        return $this->showAll($trans,200,TransactionData::class);
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
