<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage', 25);
        $page = $request->query('page', 1);
        $products = Product::
        when($request->name,function($query)use($request){
            $key = explode(' ', $request->name);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->when($request->sku,function($query)use($request){
            $key = explode(' ', $request->sku);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('sku', 'like', "%{$value}%");
                }
            });
        })
        ->paginate($perPage,'*','*',$page);
        return response()->json([
            "currentPage" => $page,
            "perPage"     => $perPage,
            "total"       => $products->total(),
            "data"        => $products->items()
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                'name'=>'required',
                'sku'=>'required|unique:products,sku',
                'brand_id'=>'required|exists:brands,id',
                'category_id'=>'required|exists:categories,id',
                'price'=>'required|numeric'
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message'=> 'validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
    
            $product = Product::create([
                'name'        => $request->name,
                'sku'         => $request->sku,
                'brand_id'    => $request->brand_id,
                'category_id' => $request->category_id,
                'usp'         => $request->usp ?? '',
                'price'       => $request->price,
                'qty'         => $request->qty ?? 0,
                'description' => $request->description ?? ""
            ]);
    
            return response()->json([
                'status'=>true,
                'message'=>trans('messages.created successfully',['model'=>'Product']),
                'data'=>$product
            ], 200);
        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message'=> $th->getMessage(),
                'line'=>$th->getLine(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try{
            $validator = Validator::make($request->all(),[
                'sku'=>'nullable|sometimes|unique:products,sku,except,'.$product->id,
                'brand_id'=>'nullable|sometimes|exists:brands,id',
                'category_id'=>'nullable|sometimes|exists:categories,id'
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message'=> 'validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
    
            $product->name        = $request->name ? $request->name : $product->name;
            $product->sku         = $request->sku ? $request->sku : $product->sku;
            $product->brand_id    = $request->brand_id ? $request->brand_id : $product->brand_id;
            $product->category_id = $request->category_id ? $request->category_id : $product->category_id;
            $product->description = $request->description;
            $product->usp         = $request->usp;
            $product->save();
    
            return response()->json([
                'status'=>true,
                'message'=>trans('messages.created successfully',['model'=>'Product']),
                'data'=>$product
            ], 200);
        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message'=> $th->getMessage()
            ], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try{
            $product->delete();    
            return response()->json([
                'status'=>true,
                'message'=>trans('messages.deleted successfully',['model'=>'Product']),
                'data'=>$product
            ], 200);
        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message'=> $th->getMessage()
            ], 500);
        }
    }
}
