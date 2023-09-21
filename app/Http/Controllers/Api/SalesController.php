<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sales;
use App\Models\SalesDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    public function index(Request $request){
        $perPage = $request->query('perPage', 25);
        $page = $request->query('page', 1);
        $sales = Sales::with('salesDetails')
        ->paginate($perPage,'*','*',$page);
        return response()->json([
            "currentPage" => $page,
            "perPage"     => $perPage,
            "total"       => $sales->total(),
            "data"        => $sales->items()
        ],200);
    }

    public function generateSales(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'products'=>'required|array',
                'paid_amount'=>'required|numeric'
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message'=> 'validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
            DB::beginTransaction();
            $sales = Sales::create([
                'price'       => 0,
                'paid_amount' => $request->paid_amount,
                'remarks'     => $request->remarks
            ]);
            $salesDetails = [];
            $totalAmount = 0;
            foreach($request->products as $item){
                if(!$product = Product::find($item['id'])){
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message'=> trans('messages.not found',['model'=>'One or More Product'])
                    ], 404);
                }
                if($product->qty < $item['qty']){
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message'=> trans('messages.out of stock',['product'=>$product->name])
                    ], 400);
                }

                $salesDetails[] = [
                    'sales_id' => $sales->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'unit' => $item['unit'] ?? 'piece',
                    'price' => $product->price
                ];
                $totalAmount += $item['qty']*$product->price;
                $product->decrement('qty', $item['qty']);
            }
            $sales->price = $totalAmount;
            $sales->save();
            SalesDetails::insert($salesDetails);
            
            DB::commit();
            return response()->json([
                'status'=>true,
                'message'=>trans('messages.created successfully',['model'=>'Sales']),
                'data'=>$sales
            ], 200);
        }catch(\Throwable $th){
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'=> $th->getMessage(),
                'line'=>$th->getLine()
            ], 500);
        }
    }
}
