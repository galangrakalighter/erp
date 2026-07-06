<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Models\Sales;
use App\Models\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    /**
     * Menampilkan daftar purchase order dengan filter dan pencarian.
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi query dasar
        $query = PurchaseOrder::with(['details.barang']);

        // 2. Batasi akses berdasarkan cabang user (Keamanan)
        if (Auth::user()->cabang !== 'Pusat') {
            $query->where('cabang', Auth::user()->cabang);
        }

        // 3. Terapkan filter yang dinamis
        $purchaseOrders = $query
            ->when($request->search, function ($q, $search) {
                $q->where(fn($sub) => $sub->where('nama_pemesan', 'like', "%{$search}%")
                    ->orWhere('nama_tempat', 'like', "%{$search}%")
                    ->orWhere('salesman', 'like', "%{$search}%")
                    ->orWhere('no_film', 'like', "%{$search}%"));
            })
            ->when($request->cabang && Auth::user()->cabang === 'Pusat', function ($q) use ($request) {
                $q->where('cabang', $request->cabang);
            })
            ->when($request->tanggal, fn($q, $tgl) => $q->whereDate('tanggal_pesan', $tgl))
            ->get();
            // ->paginate(10);
        
            // dd($purchaseOrders);
        
        $sales = Sales::where('cabang', Auth::user()->cabang)->get();

        // 4. Optimasi pengambilan warehouse
        $warehouse = (Auth::user()->cabang === 'Pusat') 
            ? Warehouse::all() 
            : Warehouse::where('cabang', Auth::user()->cabang)->get();

        return view('purchase-order', compact('purchaseOrders', 'warehouse', 'sales'));
    }

    /**
     * Menyimpan data PO baru beserta detailnya.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'nama_pemesan' => 'required',
            'alamat_pemesan' => 'required',
            'nama_tempat' => 'required', 
            'alamat_tempat' => 'required',
            'judul_cetak' => 'required', 
            'isi_per_box' => 'required', 
            'uang_muka' => 'nullable', 
            'sisa_pembayaran' => 'nullable',
            'tanggal_pesan' => 'required', 
            'jumlah_ply' => 'nullable', 
            'perporasi' => 'nullable', 
            'jumlah_box' => 'required',
            'no_film' => 'nullable', 
            'salesman' => 'nullable', 
            'keterangan' => 'nullable', 
            'tipe_pemesanan' => 'required', 
            'cabang' => 'required', 
            'ukuran' => 'nullable',
            'harga_per_box' => 'required',
            'total_order' => 'required',
            'terbilang' => 'required'
        ]);
        $validated['harga_per_box'] = str_replace('.', '', $request->harga_per_box);
        $validated['total_order'] = str_replace('.', '', $request->total_order);
        $validated['uang_muka'] = str_replace('.', '', $request->uang_muka ?? 0);
        $validated['sisa_pembayaran'] = str_replace('.', '', $request->sisa_pembayaran ?? 0);

                $po = PurchaseOrder::create($validated);

                foreach ($request->barang_id as $index => $barangId) {
                    PurchaseOrderDetail::insert([
                        'po_id'       => $po->id, // Mengambil ID dari objek $po yang baru dibuat
                        'barang_id'   => $barangId,
                        'jumlah_beli' => $request->jumlah_beli[$index],
                    ]);
                }

            return back()->with('success', 'Purchase Order berhasil dibuat.');
    }

    /**
     * Menampilkan detail PO (untuk modal detail/AJAX).
     */
    public function show($id)
    {
        // return response()->json($purchaseOrder->load('details.barang'));
    }

    /**
     * Mengambil data untuk modal edit.
     */
    public function edit($id)
    {
        $po = PurchaseOrder::with('details')->findOrFail($id);

        return response()->json([
            'id' => $po->id,
            'nama_pemesan' => $po->nama_pemesan,
            'alamat_pemesan' => $po->alamat_pemesan,
            'nama_tempat' => $po->nama_tempat,
            'alamat_tempat' => $po->alamat_tempat,
            'tanggal_pesan' => $po->tanggal_pesan,
            'salesman' => $po->salesman,
            'cabang' => $po->cabang,
            'tipe_pemesanan' => $po->tipe_pemesanan,
            'judul_cetak' => $po->judul_cetak,
            'ukuran' => $po->ukuran,
            'jumlah_box' => $po->jumlah_box,
            'harga_per_box' => $po->harga_per_box,
            'uang_muka' => $po->uang_muka,
            'jumlah_ply' => $po->jumlah_ply,
            'no_film' => $po->no_film,
            'isi_per_box' => $po->isi_per_box,
            'perporasi' => $po->perporasi,
            'sisa_pembayaran' => $po->sisa_pembayaran,
            'terbilang' => $po->terbilang,
            'total_order' => $po->total_order,
            'keterangan' => $po->keterangan,
            'detail' => $po->details
        ]);
    }

    public function getBarang($id){
        $data = Warehouse::find($id);
        return response()->json($data);
    }
    

    public function print($id)
    {
        $po = PurchaseOrder::with('details.barang')->findOrFail($id);

        $pdf = Pdf::loadView('pdf.po', compact('po'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('PO-'.$po->id.'.pdf');
    }

    public function quotation($id)
    {
        $po = PurchaseOrder::with('details.barang')->findOrFail($id);

        $pdf = Pdf::loadView('pdf.penawaran', compact('po'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('PO-'.$po->id.'.pdf');
    }

    public function invoice($id)
    {
        $po = PurchaseOrder::with('details.barang')->findOrFail($id);

        $pdf = Pdf::loadView('pdf.invoice', compact('po'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('PO-'.$po->id.'.pdf');
    }
    public function faktur($id)
    {
        $po = PurchaseOrder::with('details.barang')->findOrFail($id);

        $pdf = Pdf::loadView('pdf.faktur', compact('po'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('PO-'.$po->id.'.pdf');
    }

    public function suratJalan($id)
    {
        $po = PurchaseOrder::with('details.barang')
                ->findOrFail($id);

        $pdf = Pdf::loadView(
            'pdf.surat-jalan',
            compact('po')
        );

        return $pdf->stream('surat-jalan.pdf');
    }

    /**
     * Memperbarui Purchase Order dan detailnya.
     */
    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::find($id);
        $validated = $request->validate([
            // Sama dengan validasi store
            'nama_pemesan' => 'required|string',
            // ... (tambahkan validasi serupa dengan store)
        ]);

        try {
            DB::transaction(function () use ($request, $purchaseOrder, $validated) {
                $purchaseOrder->update($validated);
                
                // Hapus detail lama dan buat ulang
                $purchaseOrder->details()->delete();
                foreach ($request->barang_id as $index => $barangId) {
                    $purchaseOrder->details()->create([
                        'barang_id' => $barangId,
                        'jumlah_beli' => $request->jumlah_beli[$index],
                    ]);
                }
            });

            return back()->with('success', 'Purchase Order berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Update gagal: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus PO.
     */
    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::find($id);
        try {
            $purchaseOrder->delete();
            return back()->with('success', 'Purchase Order berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}