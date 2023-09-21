<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage', 25);
        $page = $request->query('page', 1);
        $unit = Unit::
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
            "total"       => $unit->total(),
            "data"        => $unit->items()
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
                'name'=>'required|unique:units,name'
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message'=> 'validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
    
            $unit = Unit::create([
                'name'    => $request->name
            ]);
    
            return response()->json([
                'status'=>true,
                'message'=>trans('messages.created successfully',['model'=>'Unit']),
                'data'=>$unit
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
    public function show(Unit $unit)
    {
        return response()->json($unit);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        try{
            $validator = Validator::make($request->all(),[
                'name'=>'required|unique:units,name,except,'.$unit->id
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message'=> 'validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
    
            $unit->name = $request->name;
            $unit->save();
    
            return response()->json([
                'status'=>true,
                'message'=>trans('messages.updated successfully',['model'=>'Unit']),
                'data'=>$unit
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
    public function destroy(Unit $unit)
    {
        try{
            $unit->delete();    
            return response()->json([
                'status'=>true,
                'message'=>trans('messages.deleted successfully',['model'=>'Unit']),
                'data'=>$unit
            ], 200);
        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message'=> $th->getMessage()
            ], 500);
        }
    }
}
