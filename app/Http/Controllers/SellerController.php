<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Http\Requests\StoreSellerRequest;
use App\Http\Requests\UpdateSellerRequest;
use App\Data\SellerData;

class SellerController extends apiController
{
     protected function getCacheTags(): array
    {
        return ['api', 'sellers', 'users'];
    }
    public function index()
    {
        $seller = Seller::has('product')->get();

        if(!$seller)
        {
            return $this->errorResponse(['message'=>'user not found','code'=>400],400);
        }
        return $this->showAll($seller,200,SellerData::class);
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
    public function store(StoreSellerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Seller $seller)
    {
        // $seller = Seller::has('product')->find($id);
          $seller = SellerData::from($seller);

        if(!$seller)
        {
            return $this->errorResponse(['message'=>'seller not found','code'=>400],400);
        }

        return response()->json($seller);
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
    public function update(UpdateSellerRequest $request, Seller $seller)
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
