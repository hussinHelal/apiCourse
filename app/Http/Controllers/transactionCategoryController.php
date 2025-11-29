<?php

namespace App\Http\Controllers;

use App\Data\CategoryData;
use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\Categories;
class transactionCategoryController extends apiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Transactions $transactions)
    {
        $t = Transactions::all()->load('product.categories');
        // $t = $transactions->product->categories;
        // $p = $t->pluck('product');
        // $cat = $p->pluck('categories');
        return $this->showAll($t,200,CategoryData::class);
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
        $t = Transactions::find($id);

        if(!$t)
        {
            return $this->errorResponse('Transaction not found', 404);
        }

        $t->load('product.categories');
        $cat = $t->product->categories;

        return response()->json($cat);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transactions $transactions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transactions $transactions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transactions $transactions)
    {
        //
    }
}
