<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class CartController extends Controller
{
    public function addToCart($id)
    {
        try {
            $cart = new Cart();
            $cart->user_id = Auth::user()->user_id;
            Log::debug(Auth::user()->user_id);
            $cart->product_id = $id;
            $cart->save();
            return response()->json(['success' => true, 'message' => 'Item added successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function removeFromCart($id)
    {
        try {
            $cart = Cart::where('product_id', $id)->first();
            $cart->delete();
            return response()->json(['success' => true, 'message' => 'removed successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function fetchCartItems(Request $request)
{
    try {
        // $userId = Auth::user()->user_id;
        $userId = $request->id;

        // Fetch the user model from the database
        $user = User::where('user_id', $userId)->first();

        if ($user) {
            // Now that you have the user model, you can access the cartItems relationship
            $cartItems = $user->cartItems()->with('product')->get();

            return response()->json(['success' => true, 'cartItems' => $cartItems]);
        } else {
            // Handle the case where the user is not found
            return response()->json(['success' => false, 'message' => 'User not found']);
        }
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}
}
