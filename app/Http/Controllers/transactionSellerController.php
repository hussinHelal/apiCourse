<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Data\SellerData;
class transactionSellerController extends apiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $t = Transactions::all()->load('product.seller');
        return $this->showAll($t,200,SellerData::class);
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
    public function show(Transactions $transaction, $id)
    {
        $trans = Transactions::with('product.seller')->find($id);
        $trans = $trans->product->seller;
        return response()->json($trans);
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
