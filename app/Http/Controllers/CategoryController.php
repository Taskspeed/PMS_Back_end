<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //


    public function fetchCategory(){

    $cat = Category::select('id','categories_name')->get();

    return response()->json($cat);

    }
}
