<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function index()
    {
        //
        $products = Product::getAll()->values()->all();
        return view('products.index')->with(['products' => $products]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {
        //
        $rules = [
            'name' => 'required|string|min:2',
            'quantity' => 'required|numeric|min:1',
            'price' => 'required|numeric'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            return response()->json(['status' => 402, 'data' => ['messages' => $validator->errors()]]);
        }

        $name_check = Product::getAll()->where('name', $request->get('name'))->values()->all(); // check if name is unique
        if (count($name_check) > 0)
        {
            $validator->getMessageBag()->add('name', 'Product with this name already exists');
            return response()->json(['status' => 402, 'data' => ['messages' => $validator->errors()]]);
        }

        $product = new Product($request->all());
        $product->id = time(); // adding temp unique id. could be made more complex
        $product->created_at = date("Y-m-d H:i:s");
        try {
            $products = Storage::disk('public')->exists('products.json') ?
                json_decode(Storage::disk('public')->get('products.json')) :
                []; // get products, if file is deleted will be an empty array

            array_push($products,$product->toArray());

            Storage::disk('public')->put('products.json', json_encode($products));

        } catch(\Exception $e) {

            $validator->getMessageBag()->add('name',  $e->getMessage());
            return response()->json(['status' => 402, 'data' => ['messages' => $validator->errors()]]);

        }
        return response()->json(['status' => 200, 'data' => $product]);

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function edit(Request $request)
    {
        $rules = [
            'name' => 'required|string|min:2',
            'quantity' => 'required|numeric|min:1',
            'price' => 'required|numeric',
            'id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            return response()->json(['status' => 402, 'data' => ['messages' => $validator->errors()]]);
        }

        $name_check = Product::getAll()->where('name', $request->get('name'))
            ->where('id', '!=', (int) $request->get('id'))
            ->values()->all();
        if (count($name_check) > 0) // check if updated name is not taken by another product
        {
            $validator->getMessageBag()->add('name', 'Product with this name already exists');
            return response()->json(['status' => 402, 'data' => ['messages' => $validator->errors()]]);
        }

        $products = collect(Product::getAll()->values()->all());
        $product = $products->where('id', $request->get('id'))->first(); // find item from collection
        $product->name = $request->get('name');
        $product->price = $request->get('price');
        $product->quantity = $request->get('quantity');
        $new_products = $products->reject(function ($item) use ($request){
            return $item->id == (int) $request->get('id');
        }); // get all items from old collection without updated one
        $new_products = $new_products->toArray();
        array_push($new_products, (array)$product); // push updated one in a new array where it was deleted
        Storage::disk('public')->put('products.json', json_encode(array_values($new_products)));

        return response()->json(['status' => 200, 'data' => $product]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request)
    {
        //
        $rules = [
            'id' => 'required|integer'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            return response()->json(['status' => 402, 'data' => ['messages' => $validator->errors()]]);
        }

        $name_check = Product::getAll()->where('id', $request->get('id'))->values()->all();
        if (count($name_check) == 0) // check if exists
        {
            $validator->getMessageBag()->add('id', 'This product does not exist.');
            return response()->json(['status' => 402, 'data' => ['messages' => $validator->errors()]]);
        }

        $products = collect(Product::getAll()->values()->all());
        $new_products = $products->reject(function ($item) use ($request){
            return $item->id == (int) $request->get('id');
        }); // remove with the same algorithm as in edit
        $new_products = $new_products->toArray();

        Storage::disk('public')->put('products.json', json_encode(array_values($new_products)));

        return response()->json(['status' => 200]);
    }
}
