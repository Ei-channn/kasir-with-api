<?php

namespace App\Http\Controllers\Api;

use App\Models\DetailJual;
use App\Models\Jual;
use App\Models\Barang;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\apiResource;

class detailJualController extends Controller
{
    public function index(){
        $DetailJual = DetailJual::with('barang')->get();

        return new apiResource($DetailJual, true, 'Data Berhasil Ditampilkan');
    }

    public function store(Request $request){
        $validator = validator::make($request->all(), [
            'no_bon' => 'required|string|exists:juals,no_bon',
            'barang' => 'required|array',
            'barang.*.kode_barang' => 'required|string|exists:barangs,kode_barang',
            'barang.*.jumlah' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 442);
        };

        $array = [];

        foreach ($request->barang as $item ) {
            $barangData = Barang::where('kode_barang', $item['kode_barang'])->firstOrFail();

            $detail = DetailJual::create([
                'no_bon' => $request->no_bon,
                'kode_barang' => $item['kode_barang'],
                'harga' => $barangData->harga,
                'jumlah' => $item['jumlah'],
            ]);

            $array[] = $detail;
        }
    
        return new apiResource($array, true, 'Data Berhasil Ditambah');
    }

    public function show($id){
        $detail = DetailJual::find($id);

        return new apiResource($detail, true, 'Data Berhasil Ditampilkan');
    }

    public function update(Request $request, $id){
        $validator = validator::make($request->all(), [
            'kode_barang' => 'nullable|string|exists:barangs,kode_barang',
            'jumlah' => 'nullable|numeric',
        ]); 

        if ($validator->fails()) {
            return response()->json($validator->error(), 442);
        }

        $detail = DetailJual::find($id);

        if(is_null($detail)) {
            return response()->json(['massage' => 'Data Tidak Ditemukan'], 404);
        }

        $harga = $detail->harga;

        if ($request->kode_barang) {
            $barangData = Barang::where('kode_barang', $request->kode_barang)->firstOrFail();
            $harga = $barangData->harga;
        }

        $detail->update([
            'kode_barang' => $request->kode_barang ?? $detail->kode_barang,
            'harga' => $barangData->harga,
            'jumlah' => $request->jumlah ?? $detail->jumlah,
        ]);

        return new apiResource($detail, true, 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        $detail = DetailJual::find($id);

        if (is_null($detail)) {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }

        $detail->delete();

        return new apiResource(null, true, 'Data Berhasil Dihapus');
    }

}
