<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use App\Data\ProductData;

class ProductController extends apiController
{
     protected function getCacheTags(): array
    {
        return ['api', 'products', 'categories', 'sellers'];
    }
    public function index()
    {
        $products = Product::all();

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
        $valitdate = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'status' => 'required',
            'quantity' => 'required',
            'image' => 'nullable',
            'seller' => 'required'
        ]);

        $product = Product::create($valitdate);

        return $this->showOne($product,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product = Product::find($product->id);
        $products = ProductData::from($product);

        if(!$products){
            return $this->errorResponse("product not found.",404);
        }

        return response()->json($products,200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $product->fill($request->only(["name","description","status","quantity","image","seller_id"]));

        if($product->isClean())
        {
            return $this->errorResponse("you need to change a value to update",401);
        }

        $product->save();

        return $this->showOne($product,201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return $this->showOne($product,205);
    }
}
