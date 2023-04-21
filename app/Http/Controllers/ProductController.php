<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use \Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::with('images')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
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
            $source_image = $image;
            $destinationPath = 'public/images/products';
            $imageName = Str::uuid() . "." . $source_image->clientExtension();
            $source_image->storeAs($destinationPath, $imageName);
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
            'product' => $images
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
