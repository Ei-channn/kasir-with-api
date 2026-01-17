<?php

namespace App\Http\Controllers\Api;

use App\Models\Barang;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\apiResource;

class barangController extends Controller
{
    public function index(){
        return response()->json(Barang::all());
    }

    public function store(Request $request){
        $validator = validator::make($request->all(), [
            'kode_barang' => 'required|string',
            'nama_barang' => 'required|string',
            'harga' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 442);
        };

        $barang = Barang::create($request->all());

        return new apiResource($barang, true, 'Data Berhasil Ditambah');
    }

    public function show($id){
        $barang = Barang::find($id);

        return new apiResource($barang, true, 'Data Berhasil Ditampilkan');
    }    

    public function update(Request $request, $id){

        $barang = Barang::findOrFail($id);

        if (is_null($barang)) {
            return response()->json(['message' => 'Data Tidak Diterima'], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode_barang' => 'nullable|string',
            'nama_barang' => 'nullable|string',
            'harga' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 442);
        }

        $barang->update([
            'kode_barang' => $request->kode_barang ?? $barang->kode_barang,    
            'nama_barang' => $request->nama_barang ?? $barang->nama_barang,
            'harga' => $request->harga ?? $barang->harga,
        ]);

        return new apiResource($barang, true, 'Data Berhasil Diupdate');
    }

    public function destroy($id) {

        $barang = Barang::findOrFail($id);

        if (is_null($barang)) {
            return response()->json(['message' => 'Data Tidak Diterima'], 404);
        }

        $barang->delete();

        return new apiResource($barang, true, 'Data Item Berhasil Dihapus');
    }
    
}
