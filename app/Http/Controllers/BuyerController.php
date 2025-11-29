<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Http\Requests\StoreBuyerRequest;
use App\Http\Requests\UpdateBuyerRequest;
use App\Data\BuyerData;
class BuyerController extends apiController
{

    protected function getCacheTags(): array
    {
        return ['api', 'buyers', 'users'];
    }

    public function index()
    {
        $buyer = Buyer::has('transactions')->get();
        return $this->showAll($buyer,200,BuyerData::class);
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
    public function store(StoreBuyerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Buyer $buyer)
    {
        // $buyer = Buyer::has('transactions')->find($id);
        $buyer = BuyerData::from($buyer);
        if(!$buyer){
            return $this->errorResponse(['message' => 'Buyer Not Found','code' => 400],400);
        }
        return response()->json($buyer);
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
    public function update(UpdateBuyerRequest $request, Buyer $buyer)
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
