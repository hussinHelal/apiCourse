<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use App\Data\CategoryData;
use Illuminate\Support\Facades\Cache;

class CategoriesController extends apiController
{
       protected function getCacheTags(): array
    {
    
        return ['api', 'categories'];
    }

    public function index()
    {
        $cat = Categories::all();

        return $this->showAll($cat,200,CategoryData::class);
    }

   
    public function create()
    {
        //
    }

   
   public function store(Request $request)
    {
        Cache::flush(); 
        
        $validate = $request->validate([
            "name"=> "required",
            "description"=> "required",
        ]);
        
        $cat = Categories::create($validate);
      
        return response()->json(['success' => true, 'data' => $cat], 201);
    }

    
    public function show(Categories $category)
    {
        $cat = CategoryData::from($category);
        return response()->json($cat);
    }

  
    public function edit(Categories $categories)
    {
        //
    }

    
    public function update(Request $request, Categories $category)
    {
        $category->fill($request->only(['name','description']));

        if($category->isClean())
        {
            return $this->errorResponse('you should change something so you can update ',422);
        }

        $category->save();

        return $this->showOne($category,201);
    }

    
    public function destroy(Categories $category)
    {
        $category->delete();

        return $this ->showOne($category,202);
    }
}
