<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            "categories" => CategoryResource::collection($categories),
        ], 200);
    }



    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string|unique:categories,category',
        ]);
        $category=Category::create($data);
        return response()->json([
            "success" => "category was added successfully!",
            "category"=>new CategoryResource($category),
        ], 200);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'category' => 'required|string|unique:categories,category,' . $category->id,
        ]);
        $category->update($data);
        return response()->json([
            "success" => "category was Updated successfully!",
            "category"=>new CategoryResource($category),
        ], 200);
    }


    public function destroy(string $id)
    {
        $category = Category::with('topics')->findOrFail($id);
        // dd($category->topics->count());
        if ($category->topics->count() != 0) {
            return response()->json([
                "error" => "category cant be deleted!",
            ], 300);
        }
        $category->delete();
        return response()->json([
            "success" => "category was deleted successfully!",
        ], 200);
    }
}
