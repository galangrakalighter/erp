<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\SPKWarehouse;
use App\Models\SPKProduction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
class SPKController extends Controller
{

    public function warehouse($id)
    {

        $quotation = Quotation::with([
            'items.inventory',
            'requestPlat'
        ])
        ->findOrFail($id);


        return view('spk.warehouse',compact('quotation'));

    }



    public function production($id)
    {

        $quotation = Quotation::with([
            'items.inventory',
            'requestPlat'
        ])
        ->findOrFail($id);


        return view('spk.production',compact('quotation'));

    }

    public function sendWarehouse(Request $request, $id){
        $request->validate([
            'note' => 'nullable|string|max:1000'
        ]);

        $quotation = Quotation::findOrFail($id);

        // Cek apakah SPK Warehouse sudah pernah dibuat
        $exists = SPKWarehouse::where('quotation_id', $quotation->id)->exists();

        if ($exists) {
            return response()->json([
                'message' => 'SPK Warehouse sudah pernah dibuat.'
            ], 422);
        }

        $spk = SPKWarehouse::create([
            'quotation_id' => $quotation->id,
            'spk_number'   => 'SPK-WH-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
            'status'       => 0,
            'cabang'       => $request->cabang,
            'catatan'      => $request->note
        ]);

        return response()->json([
            'message' => 'SPK berhasil dikirim ke Warehouse.',
            'data'    => $spk
        ]);
    }

    public function sendProduction(Request $request, $id)
    {
        $request->validate([

            'pic_production_id' => 'required|exists:users,id',

            'note' => 'nullable|string|max:1000'

        ]);



        // $id adalah ID SPK Warehouse
        $warehouseSpk = SPKWarehouse::where('quotation_id', $id)->first();

        // Cek apakah production sudah dibuat
        $exists = SPKProduction::where('spk_warehouse_id', $warehouseSpk->id)->exists();

        if($exists){

            return response()->json([

                'message' => 'SPK Production sudah pernah dibuat.'

            ],422);

        }



        $spk = SPKProduction::create([

            'spk_warehouse_id' => $warehouseSpk->id,

            'spk_number' => 
                'SPK-PROD-' 
                . now()->format('Ymd')
                . '-'
                . strtoupper(Str::random(5)),


            'pic_id' => 
                $request->pic_production_id,


            'status' => 0,


            'cabang' => $warehouseSpk->cabang,


            'catatan' => $request->note

        ]);



        return response()->json([

            'message' => 'SPK berhasil dikirim ke Production.',

            'data' => $spk

        ]);

    }

    public function pdf(SPKWarehouse $spk)
    {
        $spk->load([
            'quotation.items.inventory'
        ]);

        $pdf = Pdf::loadView('spk.warehouse', [
            'spk' => $spk
        ]);

        return $pdf->stream($spk->spk_number . '.pdf');
    }

    public function accept(SPKWarehouse $spk)
    {
        if($spk->status != 0){

            return response()->json([
                'message' => 'SPK sudah diproses.'
            ],422);

        }

        $spk->update([
            'status' => 1
        ]);

        return response()->json([
            'message' => 'SPK berhasil diterima.'
        ]);
    }

    public function cancel(SPKWarehouse $spk)
    {
        if($spk->status != 1){

            return response()->json([
                'message' => 'SPK sudah diproses.'
            ],422);

        }

        $spk->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => 'SPK berhasil dibatalkan.'
        ]);
    }

    public function pdfProduction(SPKProduction $spk)
    {
        $spk->load([
            'warehouse.quotation.items.inventory',
            'pic'
        ]);

        $pdf = Pdf::loadView('spk.production', [
            'spk' => $spk
        ]);

        return $pdf->stream($spk->spk_number . '.pdf');
    }

    public function acceptProduction(SPKProduction $spk)
    {
        if ($spk->status != 0) {

            return response()->json([
                'message' => 'SPK sudah diproses.'
            ], 422);

        }

        $spk->update([
            'status' => 1
        ]);

        return response()->json([
            'message' => 'SPK berhasil diterima.'
        ]);
    }

    public function cancelProduction(SPKProduction $spk)
    {
        if ($spk->status != 1) {

            return response()->json([
                'message' => 'SPK belum sedang diproses.'
            ], 422);

        }

        $spk->update([
            'status' => 0
        ]);

        return response()->json([
            'message' => 'SPK berhasil dibatalkan.'
        ]);
    }

}