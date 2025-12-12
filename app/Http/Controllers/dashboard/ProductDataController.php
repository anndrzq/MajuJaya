<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductDataController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    return '
                        <div class="d-flex gap-2">
                            <button onclick="editProduct(\''.$row->KdProduct.'\')" class="btn btn-success btn-sm">Edit</button>
                            <button onclick="deleteProduct(\''.$row->KdProduct.'\')" class="btn btn-danger btn-sm">Hapus</button>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('content.dashboard.masterData.ProductData.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        if (!$request->product_id) {
            $lastProduct = Product::orderBy('KdProduct', 'desc')->first();
            if (!$lastProduct) {
                $code = 'PRD-001';
            } else {
                $lastNumber = (int) substr($lastProduct->KdProduct, 4);
                $code = 'PRD-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            }
            $validated['KdProduct'] = $code;
        } else {
            $validated['KdProduct'] = $request->product_id;
        }

        $product = Product::updateOrCreate(
            ['KdProduct' => $validated['KdProduct']],
            $validated
        );

        return response()->json(['status' => 'success', 'data' => $product]);
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['status' => 'success']);
    }
}
