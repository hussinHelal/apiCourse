<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class TestCategoryController extends Controller
{
    public function testStore(Request $request)
    {
        // No validation, no observers, no nothing
        $cat = new Categories();
        $cat->name = 'Test Name';
        $cat->description = 'Test Description';
        $cat->save();
        
        return response()->json([
            'message' => 'Direct test',
            'category' => $cat,
            'category_id' => $cat->id,
            'category_class' => get_class($cat)
        ]);
    }
}