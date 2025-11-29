<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductCategoryController extends apiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Product $product)
    {
        
        // $cat = $product->categories;
        // $cat = $product->categories()->get();
        $cat = Categories::with('products')->get()->unique('id')->values();
            
        return response()->json($cat);
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
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, Categories $category)
    {
        $catId = $category->id;
        $product->categories()->sync($catId);

        return response()->json($product->categories, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, Categories $category)
    {
        if(!$product->categories()->find($category->id))
        {
            return response()->json(['error'=>'the specified category is not a category of the product'],404);
        }

        $product->categories()->detach($category->id);

        return response()->json($product->categories, 200);
    }
}
