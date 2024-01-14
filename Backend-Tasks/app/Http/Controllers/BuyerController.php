<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\PurchaseTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyerController extends Controller
{
    public function index()
    {
        try {

            // $products = Products::orderBy('product_id')->paginate(4);
            $products = Products::where('is_active',1)->get();
            return response()->json(['success' => true, 'products' => $products]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }

    }


    public function orderHistory(Request $request){
        try {
            // $userId=Auth::user()->user_id;
            $userId =$request->id;
            $products = PurchaseTransaction::with('product')->where('purchased_by',$userId)->get();
            return  response()->json(['success'=> true,'products'=> $products]);
        } catch (\Exception $e) {
            return response()->json(['success'=> false,'msg'=> $e->getMessage()]);
        }
    }

    public function walletDetails(Request $request){
        try {
            \Log::debug($request->all());
            $userId=$request->id;

            $user = User::where('user_id',$userId)->first('wallet_balance');
            \Log::debug($user);
            return response()->json(['success'=> true,'data'=> $user],200);
        } catch (\Exception $e) {
            return response()->json(['success'=> false,'msg'=> $e->getMessage()]);
        }
    }
}
