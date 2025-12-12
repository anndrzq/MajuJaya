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
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi Anda telah habis. Silakan login kembali.'
            ], 401);
        }

        $rawPay = str_replace(['Rp', '.', ' ', ',-'], '', $request->pay_amount);
        $rawTotal = str_replace(['Rp', '.', ' ', ',-'], '', $request->total_price);

        $request->merge([
            'pay_amount' => $rawPay,
            'total_price' => $rawTotal,
        ]);

        $request->validate([
            'KdProduct' => 'required|array',
            'qty' => 'required|array',
            'pay_amount' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $totalPrice = (float) $request->total_price;
            $payAmount = (float) $request->pay_amount;

            if ($payAmount < $totalPrice) {
                throw new \Exception("Pembayaran kurang!");
            }

            $sale = Sale::create([
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'total_price' => $totalPrice,
                'pay_amount' => $payAmount,
                'change_amount' => $payAmount - $totalPrice,
                'user_id' => Auth::id()
            ]);

            foreach ($request->KdProduct as $index => $kdProduct) {
                $qty = $request->qty[$index];
                if ($qty <= 0) continue;

                $product = Product::where('KdProduct', $kdProduct)->firstOrFail();

                if ($product->stock < $qty) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
                }

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->KdProduct,
                    'quantity' => $qty,
                    'selling_price' => $product->price,
                    'subtotal' => $product->price * $qty,
                ]);

                $product->decrement('stock', $qty);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
