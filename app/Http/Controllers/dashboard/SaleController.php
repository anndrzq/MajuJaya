<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('content.dashboard.transaction.index', compact('products'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Sesi habis.'], 401);
        }

        $rawPay = preg_replace('/[^0-9]/', '', $request->pay_amount);
        $rawTotal = preg_replace('/[^0-9]/', '', $request->total_price);

        $request->merge([
            'pay_amount' => (float)$rawPay,
            'total_price' => (float)$rawTotal,
        ]);

        $request->validate([
            'KdProduct' => 'required|array',
            'qty' => 'required|array',
            'pay_amount' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            if ($request->pay_amount < $request->total_price) {
                throw new \Exception("Pembayaran kurang!");
            }

            $sale = Sale::create([
                'id' => (string) Str::uuid(),
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'total_price' => $request->total_price,
                'pay_amount' => $request->pay_amount,
                'change_amount' => $request->pay_amount - $request->total_price,
                'user_id' => Auth::id()
            ]);

            foreach ($request->KdProduct as $index => $kdProduct) {
                $qty = (int)$request->qty[$index];
                if ($qty <= 0) continue;

                $product = Product::where('KdProduct', $kdProduct)->lockForUpdate()->firstOrFail();

                if ($product->stock < $qty) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi.");
                }

                SaleDetail::create([
                    'id' => (string) Str::uuid(),
                    'sale_id' => $sale->id,
                    'product_id' => $product->KdProduct,
                    'quantity' => $qty,
                    'selling_price' => $product->price,
                    'subtotal' => $product->price * $qty,
                ]);

                $product->decrement('stock', $qty);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Transaksi berhasil disimpan']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }
}
