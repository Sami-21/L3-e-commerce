<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\Str;

class ProductController extends Controller
{

    public function all()
    {
        return Product::with('images')->get();
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
            'rating' => 0,
        ]);
        //Upload the images
        $images = [];
        foreach ($request->file('images') as $i => $image) {
            $destinationPath = 'public/images/products';
            $imageName = Str::uuid() . "." . $image->clientExtension();
            $image->storeAs($destinationPath, $imageName);
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


    public function get($id)
    {
        return Product::with('images')->find($id);
    }

    public function update(ProductRequest $request, $id)
    {
        // $validated = $request->validated();

        // $product = Product::findOrFail($id);

        // $product->update($validated);


        // if (array_key_exists('deleted_images', $validated) && count($validated['deleted_images'])) {
        //     foreach ($validated['deleted_images'] as $src) {
        //         $deleted_images = Image::findOrFail($src['id']);
        //         Storage::delete('images/products/' . $deleted_images->name);
        //         $deleted_images->delete();
        //     }
        // }

        // $images = [];
        // foreach ($request->file('images') as $i => $image) {
        //     if (!array_key_exists('id', $validated['images'][$i])) {
        //         $destinationPath = 'public/images/products';
        //         $imageName = Str::uuid() . "." . $image->clientExtension();
        //         $image->storeAs($destinationPath, $imageName);
        //         $images[$i] = [
        //             'name' => $imageName,
        //             'path' => $destinationPath . "/" . $imageName,
        //         ];
        //     } else {
        //         $images = Product::findOrFail($validated['images'][$i]['id']);
        //         if (!empty($request->file('images')[$i])) {
        //             Storage::delete('images/products/' . $images->image);
        //             $images_image = $request->file('images')[$i]['name'];
        //             $destinationPath = 'images/products';
        //             $imageName = Str::uuid() . "." . $images_image->getClientOriginalExtension();
        //             $images_image->storeAs($destinationPath, $imageName);
        //             $images->update([
        //                 'name' => $imageName,
        //                 'path' => $destinationPath . "/" . $imageName,
        //             ]);
        //         } else {
        //             $images->update([
        //                 'path' => $validated['images'][$i]['path'],
        //             ]);
        //         }
        //     }
        // }

        // if (count($images)) {
        //     $product->images()->createMany($images);
        // }

        // return response()->json([
        //     'message' => 'product updated successfully',
        //     $product
        // ], 201);
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
