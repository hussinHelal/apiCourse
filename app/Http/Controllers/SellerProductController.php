<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Storage;
use App\Data\SellerData;

class SellerProductController extends apiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $seller = Seller::with('product')->get()->pluck('product')->collapse();
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
    public function store(Request $request, Seller $seller)
    {
        $validate = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = new Product();
        $product->name = $validate['name'];
        $product->description = $validate['description'];
        $product->quantity = $validate['quantity'];
        $product->status = Product::UNAVAILABLE_PRODUCT;
        $product->image = $request->image->store('','images');
        $product->seller_id = $seller->id;
        $product->save();

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Seller $seller, $id)
    {
        $sellers = Seller::with('product')->has('product')->find($id);
        // dd($sellers);
        if( !$sellers )
        {
            return $this->errorResponse("this seller does not  have products",404);
        }
        
        $products = $sellers->product;
        return response()->json($products);
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
    public function update(Request $request, Seller $seller, Product $product)
    {
        $validate = $request->validate([
            'name' => 'sometimes|required',
            'description' => 'sometimes|required',
            'image' => 'sometimes|image',
            'quantity' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:' . Product::AVAILABLE_PRODUCT . ',' . Product::UNAVAILABLE_PRODUCT,
        ]);

        $this->checkSeller($seller, $product);

        $product->fill($request->only([
            'name',
            'description',
            'quantity',
        ]));

        if($request->has('status')){
            $product->status = $validate['status'];

            if($product->isAvailable() && $product->categories()->count() == 0){
                return response()->json(['error'=>'An active product must have at least one category'],409);
            }
        }

        if($request->hasFile('image'))
        {

                Storage::delete($product->image);



            $product->image = $request->image->store('','images');

        }


        if($product->isClean()){
            return response()->json(['error'=>'You need to specify a different value to update'],422);
        }

        $product->save();

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->checkSeller($seller, $product);

        try{
            if($product->image){
             $imgdel = Storage::disk('images')->delete($product->image);
            }
        }catch(\Exception $e){
            return response()->json(['error'=>'Error deleting product image'],500);
        }

        if($imgdel){
            $product->delete();
        }

        return response()->json($product);
    }

    protected function checkSeller(Seller $seller, Product $product)
    {
        if ($seller->id != $product->seller_id) {
            throw new HttpException(422, "The specified seller is not the actual seller of the product");
        }
    }
}
