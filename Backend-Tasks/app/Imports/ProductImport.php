<?php

namespace App\Imports;

use App\Models\Products;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Log;

class ProductImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        Log::debug("Import products in database");
        $rowcount = count($rows);
        $category = 0;
        $row = $rows->toArray();
        Log::debug($row);
        for ($i = 1; $i < $rowcount; $i++) {
            $productId = Str::uuid();
    
            $product = new Products([
                'product_id' => $productId,
                "is_active" => 1,
                "title" => $row[$i][1],
                "description" => $row[$i][2],
                "stock_count" => $row[$i][3],
                "price" => $row[$i][4],
                "selling_price" => $row[$i][5],
                "created_by" => Auth::user()->user_id,
                "category_id" => $row[$i][6],
            ]);
    
            $product->save();
        }
    }
}
