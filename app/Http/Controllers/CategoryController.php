<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function all()
    {
        return response()->json([
            'categories' => Category::all()
        ]);
    }

    public function get($id)
    {
        $category = Category::with('products')->findOrFail($id);
        return response()->json([
            'category' => $category
        ]);
    }

    public function search(Request $request)
    {
        $categories = Category::where('name', 'like', '%' . $request->keyword . '%')->get();
        return response()->json([
            'categories' => $categories
        ]);
    }

    public function add(CategoryRequest $request)
    {
        $validated = $request->validated();
        $category = Category::create($validated);
        return response()->json([
            'message' => 'category created successfully',
            'category' => $category
        ], 201);
    }

    public function update(CategoryRequest $request, $id)
    {
        $validated  = $request->validated();
        $category = Category::findOrFail($id);
        $category->update($validated);
        return response()->json([
            'message' => 'category updated successfully',
            'category' => $category
        ], 201);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json([
            'message' => 'category deleted successfully',
        ], 201);
    }

    public function changeStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->status = !$category->status;
        $category->save();
        return response()->json([
            'message' => 'category status changed successfully',
            'category' => $category
        ], 201);
    }
}
