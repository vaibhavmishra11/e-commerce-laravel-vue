<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\PurchaseTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;
use Illuminate\Support\Str;
use Session;
use Exception;
use Log;

class RazorpayController extends Controller
{
    public function createOrder(Request $request)
    {
        \Log::debug("createOrder");
        \Log::debug($request->all());
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
        $order = $api->order->create([
            'amount' => $request->amount * 100,
            'currency' => 'INR',
        ]);
        \Log::debug('Order ID: ' . $order->id);
        \Log::debug('Order Amount: ' . $order->amount / 100);


        return response()->json(['order' => $order->id]);
    }


    public function capturePayment(Request $request)
    {
        try {
            $api = new Api("rzp_test_HSMiyxVS1q5Gmw", "8YVcR8yziGsi9wKFCuI4togi");
            $payment = $api->payment->fetch($request->payment_id);
            return response()->json(['success' => true, 'payment' => $payment->captured]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'payment' => $payment->captured]);
        }
    }

    public function handlePaymentFailure(Request $request)
    {
        $paymentId = $request->input('payment_id');

        return response()->json(['message' => 'Payment failure handled successfully']);
    }

    
}