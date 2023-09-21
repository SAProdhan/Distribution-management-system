<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockChallan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockChallanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage', 25);
        $page = $request->query('page', 1);
        $products = StockChallan::with('products')->paginate($perPage,'*','*',$page);
        return response()->json([
            "currentPage" => $page,
            "perPage"     => $perPage,
            "total"       => $products->total(),
            "data"        => $products->items()
        ],200);
    }

    /**
     * Update stock.
     */
    public function updateStock(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                'department_id'=>'required|exists:departments,id',
                'challan_no'=>'required|unique:stock_challans',
                'product_data'=>'required|array'
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message'=> 'validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
    
            DB::beginTransaction();
            $stockChallan = new StockChallan();
            $stockChallan->department_id = $request->department_id;
            $stockChallan->challan_no = $request->challan_no;
            $stockChallan->save();
            
            $data = [];
            foreach($request->product_data as $value){
                $data[$value['id']] = ['qty'=>$value['qty']];
                Product::whereId($value['id'])->increment('qty',$value['qty']);
            }

            $stockChallan->products()->sync($data);
            
            DB::commit();

            return response()->json([
                'status'=>true,
                'message'=>trans('messages.created successfully',['model'=>'Stock Challan']),
                'data'=>$stockChallan
            ], 200);
        }catch(\Throwable $th){
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'=> $th->getMessage()
            ], 500);
        }
    }
}
