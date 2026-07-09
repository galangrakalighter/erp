<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil data quotation beserta itemnya
        $cabang = Auth::user()->cabang;
        if($cabang == 'Pusat'){
            $inventories = Warehouse::all(); 
        }else{
            // Mengambil data inventori untuk pilihan di dropdown modal
            $inventories = Warehouse::where('cabang', $cabang)->get();    
        }


        return view('quotations', compact('inventories', 'cabang'));
    }

    public function getDataQuotation($cabang)
    {
        if ($cabang == 'Pusat') {
            $quotations = Quotation::with('items.inventory')->get();
        } else {
            $quotations = Quotation::with('items.inventory')
                ->where('cabang', $cabang)
                ->get();
        }

        return response()->json($quotations);
    }

    public function getInventories($cabang)
    {
        if ($cabang == 'Pusat') {
            $inventories = Warehouse::all();
        } else {
            $inventories = Warehouse::where('cabang', $cabang)->get();
        }

        return response()->json($inventories);
    }

    public function requestPlat(Request $request){
        $quotation = Quotation::find($request->plat_id);
        $quotation->update([
            'status' => 2
        ]);
        return response()->json(['status' => 'Berhasil Request', 'data' => $quotation]);
    }

    public function cancelPlat(Request $request){
        $quotation = Quotation::find($request->plat_id);
        $quotation->update([
            'status' => 1
        ]);
        return response()->json(['status' => 'Berhasil Request', 'data' => $quotation]);
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {

            $quotation = Quotation::with('items.inventory')
                ->where('id', $id)
                ->where('cabang', Auth::user()->cabang)
                ->firstOrFail();

            // Sudah pernah di-approve
            if ($quotation->status) {
                abort(400, 'Quotation sudah di-approve.');
            }

            // Cek ulang stok
            foreach ($quotation->items as $item) {

                if ($item->inventory->jumlah < $item->quantity) {
                    abort(
                        400,
                        "Stok {$item->inventory->barang} tidak mencukupi."
                    );
                }
            }

            // Kurangi stok
            foreach ($quotation->items as $item) {

                $warehouse = $item->inventory;

                $warehouse->decrement('jumlah', $item->quantity);
            }

            // Approve quotation
            $quotation->update([
                'status'      => 1,
                'approved_by' => Auth::id(),      // optional
                'approved_at' => now(),           // optional
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Quotation berhasil di-approve.');
    }

    public function reject($id)
    {
        DB::transaction(function () use ($id) {

            $quotation = Quotation::with('items.inventory')
                ->where('id', $id)
                ->where('cabang', Auth::user()->cabang)
                ->firstOrFail();

            // Approve quotation
            $quotation->update([
                'status'      => 0,
                'approved_by' => null,      // optional
                'approved_at' => null,           // optional
            ]);
        });

        return redirect()
            ->back()
            ->with('error', 'Quotation berhasil di-reject.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'valid_until'   => 'required|date',
            'items'         => 'required|array|min:1',
            'items.*.inventory_id' => 'required|exists:warehouse,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.unit_price'   => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // 2. Hitung total amount dari semua item
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += ($item['quantity'] * $item['unit_price']);
            }

            // 3. Simpan Header Quotation
            $quotation = Quotation::create([
                'quotation_number'     => 'Q-' . date('Ymd') . '-' . strtoupper(uniqid()),
                'nama_customer'        => $request->nama_customer,
                'valid_until'          => $request->valid_until,
                'total_amount'         => $totalAmount,
                'cabang'               => Auth::user()->cabang,
                'status'               => 0
            ]);

            // 4. Simpan Item Quotation
            foreach ($request->items as $item) {
                $quotation->items()->create([
                    'warehouse_id' => $item['inventory_id'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price'],
                    'subtotal'     => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        return redirect()->back()->with('success', 'Quotation berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'valid_until'   => 'required|date',
            'items'         => 'required|array|min:1',
            'items.*.inventory_id' => 'required|exists:warehouse,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.unit_price'   => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $id) {

            $quotation = Quotation::where('id', $id)
                ->where('cabang', Auth::user()->cabang)
                ->firstOrFail();

            // Hitung total
            $totalAmount = collect($request->items)->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            // Update header quotation
            $quotation->update([
                'nama_customer' => $request->nama_customer,
                'valid_until'   => $request->valid_until,
                'total_amount'  => $totalAmount,
            ]);

            // Hapus semua item lama
            $quotation->items()->delete();

            // Simpan ulang item
            foreach ($request->items as $item) {
                $quotation->items()->create([
                    'warehouse_id' => $item['inventory_id'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price'],
                    'subtotal'     => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        return redirect()->back()->with('success', 'Quotation berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->delete(); // Karena sudah ada onDelete('cascade') di migration, items otomatis terhapus

        return redirect()->back()->with('success', 'Quotation berhasil dihapus.');
    }

    public function checkStock(Request $request)
    {
        $request->validate([
            'quotation_id' => 'required|exists:quotations,id',
        ]);

        $quotation = Quotation::with('items.inventory')
            ->where('id', $request->quotation_id)
            ->where('cabang', Auth::user()->cabang)
            ->firstOrFail();

        $kurang = [];

        foreach ($quotation->items as $item) {

            $stok = $item->inventory->jumlah ?? 0;

            if ($stok < $item->quantity) {

                $kurang[] = [
                    'barang' => $item->inventory->barang,
                    'qty'    => $item->quantity,
                    'stock'  => $stok,
                ];
            }
        }

        if (count($kurang) > 0) {
            return response()->json([
                'success' => false,
                'items'   => $kurang
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }
}
