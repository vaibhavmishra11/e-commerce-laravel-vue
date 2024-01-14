<?php

namespace App\Http\Controllers;

use App\Imports\ProductImport;
use App\Models\Cart;
use App\Models\ProductCategory;
use App\Models\Products;
use App\Models\PurchaseTransaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Products::all();

        return response()->json(['success' => true, 'product' => $products]);
    }



    public function store(Request $request)
    {
        Log::debug($request->all());
        $categoryName = $request->category;
        $categoryId = ProductCategory::where('category_display_name', $categoryName)->first()->category_id;
        $product = new Products();
        $product->product_id = Str::uuid();
        $product->title = $request->title;
        $product->description = $request->description;
        $product->stock_count = $request->stock_count;
        $product->price = $request->price;
        $product->selling_price = $request->selling_price;
        $product->category_id = $categoryId;
        // $product->created_by = auth()->user()->user_id;
        $product->created_by = $request->created_by;
        //Log::debug(auth()->user()->user_id);
        $product->save();

        return response()->json(['success' => true, 'product' => $product], 200);
    }

    public function updateActiveStatus(Request $request)
    {
        $product = Products::where('product_id', $request->id)->first();

        if ($product) {
            $product->is_active = !$product->is_active;
            $product->save();

            return response()->json(['success' => true, 'product' => $product], 200);
        }

        return response()->json(['success' => false, 'message' => 'Product not found']);
    }

    public function show(Request $request)
    {
        try {
            Log::debug($request->all());
            $product = Products::where('product_id', $request->id)->first();
            if ($product) {
                return response()->json(['success' => true, 'product' => $product], 200);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

    }

    public function updateProduct(Request $request, $id)
    {
        try {
            Log::debug(request()->all());
            Log::debug('product_id' . $id);
            $product = Products::where('product_id', $id)->first();
            Log::debug($product);
            if ($product) {
                $categoryName = $request->category;
                $categoryId = ProductCategory::where('category_display_name', $categoryName)->first()->category_id;
                $product->title = $request->title;
                $product->description = $request->description;
                $product->price = $request->price;
                $product->stock_count = $request->stock_count;
                $product->selling_price = $request->selling_price;
                $product->category_id = $categoryId;
                $product->save();

            }
            return response()->json(['success' => true, 'product' => $product], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $checkIfOrder = PurchaseTransaction::where('product_id', $request->id)->first();
            if ($checkIfOrder) {
                return response()->json(['success' => false, 'message' => 'You can not delete this product user has purchased You can mark out of stock']);
            } else {
                $product = Products::where('product_id', $request->id)->first();
                if ($product) {
                    $product->delete();
                }
                return response()->json(['success' => true, 'message' => 'deleted successfully'], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateStockStatus(Request $request)
    {
        try {
            $product = Products::where('product_id', $request->id)->first();
            Log::debug($request->all());
            $outOfStock = $product->out_of_stock;
            if ($product) {
                $product->out_of_stock = !$outOfStock;
                $product->save();
            }
            return response()->json(['success' => true, 'product' => $product], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function saveOrderData(Request $request)
    {
        Log::debug($request->all());
        foreach ($request->payload['cartItems'] as $item) {

            $purchaseTransaction = new PurchaseTransaction();
            $purchaseTransaction->TID = Str::uuid();
            $purchaseTransaction->original_transaction_id = $request->paymentId;
            $purchaseTransaction->amount = ($item['price'] * $item['quantity']);
            $purchaseTransaction->product_id = $item['productId'];
            $purchaseTransaction->purchased_by = Auth::user()->user_id;
            $purchaseTransaction->transaction_type = 'purchase';
            $purchaseTransaction->transaction_status = 'completed';
            $purchaseTransaction->payment_purchased_id = $request->orderId;
            $purchaseTransaction->payment_method = 'wallet';
            $purchaseTransaction->quantity = $item['quantity'];
            $purchaseTransaction->save();
            $this->deleteCartItems($item['productId']);
            $this->reduceStockQuantity($item['productId'], $item['quantity']);

        }
        return response()->json(['success' => true]);
    }

    public function deleteCartItems($productId)
    {
        $userId = Auth::user()->user_id;
        $cartItems = Cart::where('product_id', $productId)->where('user_id', $userId)->first();
        $cartItems->delete();
    }

    public function fetchSoldProducts(Request $request)
    {
        try {
            $product = Products::withCount('purchaseTransactions as total_quantity')
                ->having('total_quantity', '>', 0)
                ->orderBy($request->sortBy ? $request->sortBy : 'created_at', $request->desc ? 'desc' : 'asc')
                ->paginate($request->perPage ? $request->perPage : 10);
            Log::debug($product);
            return response()->json(['success' => true, 'data' => $product], 200);

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }

    }

    public function reduceStockQuantity($productId, $quantity)
    {
        $product = Products::where('product_id', $productId)->first();
        $stockCount = $product->stock_count - $quantity;
        $product->stock_count = $stockCount;
        $product->save();
    }

    public function purchaseHistory(Request $request)
    {
        try {
            $product = PurchaseTransaction::orderBy($request->sortBy ? $request->sortBy : 'created_at', $request->desc ? 'desc' : 'asc')
                ->paginate($request->perPage ? $request->perPage : 10);
            return response()->json(['success' => true, 'data' => $product], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }

    }
    public function upload(Request $request)
    {
        Log::debug('hello9');
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
        $file = $request->file('file')->store('files');
        Excel::import(new ProductImport, $file);
        Log::debug(request()->all());

        return response()->json(['message' => 'File uploaded and processed successfully']);
    }
}
