<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use Illuminate\Http\Request;
use App\Data\TransactionData;
class BuyerTransactionController extends apiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaction = Buyer::has('transactions')->get();
        return $this->showAll($transaction,200,TransactionData::class);
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
        $buyer = $buyer->with('transactions')->get(); 
        $buyerWithTran = $buyer->map(function ($buyer){
            $tran = $buyer->transactions;

            return [
               'buyer' => $buyer,
               'transaction' => $tran
            ];
        });
        $buyerWithTran = $buyerWithTran->pluck('buyer.transactions');

        return response()->json($buyerWithTran);
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
        $buyer = Buyer::find($buyer->id);
        if(!$buyer)
        {
            return $this->errorResponse('Buyer not found', 404);
        }

        $buyer->delete();
        return $this->showOne($buyer);

    }
}
