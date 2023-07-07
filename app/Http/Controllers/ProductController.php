<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\Str;

class ProductController extends Controller
{

    public function all()
    {
        $products = Product::with('images')->get();
        return response()->json($products, 200);
    }

    public function get($id)
    {
        $product = Product::with(['category', 'images'])->findOrFail($id);
        if (!$product)
            return response()->json(['message' => 'Product not found'], 404);
        return response()->json($product, 200);
    }

    public function getByCategory($id)
    {
        $products = Product::where('category_id', $id)->with('images')->get();
        return response()->json($products, 200);
    }

    public function search(Request $request)
    {
        $products = Product::where('name', 'like', '%' . $request->keyword . '%')->with('images')->get();
        return response()->json($products, 200);
    }

    public function add(ProductRequest $request)
    {
        //Validate the request
        $request->validated();
        //Create the product
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'features' => $request->features,
            'colors' => $request->colors,
            'capacity' => $request->capacity,
            'rating' => 0,
            'category_id' => $request->category_id,
        ]);
        //Upload the images
        $images = [];
        foreach ($request->file('images') as $i => $image) {
            $destinationPath = 'images/products';
            $imageName = Str::uuid() . "." . $image->clientExtension();
            $image->move($destinationPath, $imageName);
            $images[$i] = [
                'name' => $imageName,
                'path' => $destinationPath . "/" . $imageName,
            ];
        }
        //Create the images
        $product->images()->createMany($images);
        //Return the response
        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    public function update(ProductRequest $request, $id)
    {

        $validated = $request->validated();

        $product = Product::findOrFail($id);

        $product->update($validated);


        if (array_key_exists('deleted_images', $validated) && count($validated['deleted_images'])) {
            foreach ($validated['deleted_images'] as $src) {
                $deleted_images = Image::findOrFail($src);
                unlink($deleted_images->path);
                $deleted_images->delete();
            }
        }

        $images = [];
        for ($i = 0; $i < count($validated['images']); ++$i) {
            if (!array_key_exists('id', $validated['images'])) {
                $destinationPath = 'images/products';
                $imageName = Str::uuid() . "." . $validated['images'][$i]->clientExtension();
                $validated['images'][$i]->move($destinationPath, $imageName);
                $images[$i] = [
                    'name' => $imageName,
                    'path' => $destinationPath . "/" . $imageName,
                ];
            } else {
                $image = Image::findOrFail($validated['images'][$i]['id']);
                if (!empty($request->file('images')[$i])) {
                    unlink('images/products/' . $image);
                    $images_image = $request->file('images')[$i]['name'];
                    $destinationPath = 'images/products';
                    $imageName = Str::uuid() . "." . $images_image->getClientOriginalExtension();
                    $images_image->move($destinationPath, $imageName);
                    $image->update([
                        'name' => $imageName,
                        'path' => $destinationPath . "/" . $imageName,
                    ]);
                } else {
                    $image->update([
                        'path' => $validated['images'][$i]['path'],
                    ]);
                }
            }
        }

        if (count($images)) {
            $product->images()->createMany($images);
        }

        return response()->json([
            'message' => 'product updated successfully',
            $product
        ], 201);
    }


    public function changeStatus($id)
    {
        $product = Product::findOrfail($id);
        $product->update([
            'status' => !$product->status
        ]);
        return response()->json([
            'message' => 'Product status changed successfully',
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrfail($id);
        foreach ($product->images as $image) {
            Storage::delete('images/products/' . $image->name);
            $image->delete();
        }
        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}
