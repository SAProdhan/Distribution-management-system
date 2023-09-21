<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage', 25);
        $page = $request->query('page', 1);
        $brands = Category::
        when($request->name,function($query)use($request){
            $key = explode(' ', $request->name);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->paginate($perPage,'*','*',$page);
        return response()->json([
            "currentPage" => $page,
            "perPage"     => $perPage,
            "total"   => $brands->total(),
            "data"         => $brands->items()
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
                'name'=>'required|unique:categories,name'
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message'=> 'validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
    
            $category = Category::create([
                'name'    => $request->name
            ]);
    
            return response()->json([
                'status'=>true,
                'message'=>trans('messages.created successfully',['model'=>'Category']),
                'data'=>$category
            ], 200);
        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message'=> $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        try{
            $validator = Validator::make($request->all(),[
                'name'=>'required|unique:brands,name,except,'.$category->id
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message'=> 'validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
    
            $category->name = $request->name;
            $category->save();
    
            return response()->json([
                'status'=>true,
                'message'=>trans('messages.updated successfully',['model'=>'Category']),
                'data'=>$category
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
    public function destroy(Category $category)
    {
        try{
            $category->delete();    
            return response()->json([
                'status'=>true,
                'message'=>trans('messages.deleted successfully',['model'=>'Category']),
                'data'=>$category
            ], 200);
        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message'=> $th->getMessage()
            ], 500);
        }
    }
}
