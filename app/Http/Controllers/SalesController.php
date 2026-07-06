<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use Illuminate\Support\Facades\Auth;
class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sales::query();

        if (Auth::user()->cabang !== "Pusat") {
            $query->where('cabang', Auth::user()->cabang);
        }

        // Menambahkan filter pencarian jika ada input 'search'
        $sale = $query->when($request->search, function ($q, $search) {
            $q->where('nama_sales', 'like', "%{$search}%");
            $q->where('nip', 'like', "%{$search}%");
        })
        ->when($request->cabang, fn($q, $cabang) => $q->where('cabang', $cabang))
        ->get();

        return view('sales', compact('sale'));
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
        $request->validate([
            'nip' => 'required',
            'nama_sales' => 'required'
        ]);

        Sales::create([
            'nip' => $request->nip,
            'nama_sales' => $request->nama_sales,
            'cabang' => Auth::user()->cabang
        ]);

        return redirect()->back()->with('success', 'Data Sales Berhasil Ditambahkan');
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
            'nip' => 'required',
            'nama_sales' => 'required',
        ]);

        Sales::where('id', $id)->update([
            'nip' => $request->nip,
            'nama_sales' => $request->nama_sales,
            'cabang' => Auth::user()->cabang
        ]);

        return redirect()->back()->with('success', 'Data Sales berhasil Diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sale = Sales::find($id);
        $sale->delete();
        return redirect()->back()->with('success', 'Data Sales berhasil dihapus');
    }
}
