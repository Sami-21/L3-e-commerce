<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\Str;

class ProductController extends Controller
{

    public function all(Request $request)
    {
        $products = Product::with('images')->where('name', 'LIKE', '%' . $request->q . '%')->whereHas('category', function ($q) use ($request) {
            $q->where('name', 'LIKE', '%' . $request->category . '%');
        })->paginate($request->perPage);
        return response()->json(new ProductCollection($products), 200);
    }

    public function get($id)
    {
        $product = Product::with(['category', 'images'])->findOrFail($id);
        if (!$product)
            return response()->json(['message' => 'Product not found'], 404);

        return response()->json(new ProductResource($product), 200);
    }

    public function getImages($id)
    {
        $images = Image::where('product_id', $id)->get();
        return response()->json([
            'images' => $images
        ]);
    }

    public function add(ProductRequest $request)
    {

        //Validate the request
        $request->validated();
        //Create the product
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'promotion_price' => $request->promotion_price,
            'features' => $request->features,
            'rating' => 0,
            'category_id' => $request->category_id,
        ]);
        //Upload the images
        $images = [];
        foreach ($request->file('images') as $i => $image) {
            $imageName = Str::uuid() . "." . $image->clientExtension();
            $image->storeAs('public/images/products', $imageName);
            $images[$i] = [
                'name' => $imageName,
                'path' => 'public/images/products/'  . $imageName,
            ];
        }
        //Create the images
        $product->images()->createMany($images);
        //Return the response
        return response()->json([
            'message' => 'Product created successfully',
            'product' => new ProductResource($product)
        ], 201);
    }

    public function update(ProductRequest $request, $id)
    {

        $validated = $request->validated();

        $product = Product::findOrFail($id);

        $product->update($validated);


        if (array_key_exists('deleted_images', $validated) && count($validated['deleted_images'])) {
            foreach ($validated['deleted_images'] as $src) {
                $deleted_image = Image::findOrFail($src);
                Storage::delete('public/images/products/' . $deleted_image->name);
                $deleted_image->delete();
            }
        }

        $images = [];
        for ($i = 0; $i < count($validated['images']); ++$i) {
            if (!array_key_exists('id', $validated['images'])) {
                $destinationPath = 'public/images/products/';
                $imageName = Str::uuid() . "." . $validated['images'][$i]->clientExtension();
                $validated['images'][$i]->storeAs($destinationPath, $imageName);
                $images[$i] = [
                    'name' => $imageName,
                    'path' => $destinationPath  . $imageName,
                ];
            } else {
                $image = Image::findOrFail($validated['images'][$i]['id']);
                if (!empty($request->file('images')[$i])) {
                    Storage::delete('public/images/products/' . $image);
                    $images_image = $request->file('images')[$i]['name'];
                    $destinationPath = 'public/images/products/';
                    $imageName = Str::uuid() . "." . $images_image->getClientOriginalExtension();
                    $images_image->move($destinationPath, $imageName);
                    $image->update([
                        'name' => $imageName,
                        'path' => $destinationPath  . $imageName,
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
            new ProductResource($product)
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
            Storage::delete('public/images/products/' . $image->name);
            $image->delete();
        }
        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}
