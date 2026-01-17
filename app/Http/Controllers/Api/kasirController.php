<?php

namespace App\Http\Controllers\Api;

use App\Models\Kasir;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\apiResource;

class kasirController extends Controller
{
    public function index(){
        return response()->json(Kasir::all());
    }

    public function store(Request $request){
        $validator = validator::make($request->all(), [
            'kode_kasir' => 'required|string',
            'nama_kasir' => 'required|string'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 442);
        };

        $kasir = Kasir::create($request->all());

        return new apiResource($kasir, true, 'Data Berhasil Ditambah');
    }

    public function show($id){
        $kasir = Kasir::find($id);

        return new apiResource($kasir, true, 'Data Berhasil Ditampilkan');
    }

    public function update(Request $request, $id){

        $kasir = Kasir::findOrFail($id);

        if (is_null($kasir)) {
            return response()->json(['message' => 'Data Tidak Diterima'], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode_kasir' => 'nullable|string',
            'nama_kasir' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 442);
        }

        $kasir->update([
            'kode_kasir' => $request->kode_kasir ?? $kasir->kode_kasir,    
            'nama_kasir' => $request->nama_kasir ?? $kasir->nama_kasir,
        ]);

        return new apiResource($kasir, true, 'Data Berhasil Diupdate');
    }

    public function destroy($id) {

        $kasir = Kasir::findOrFail($id);

        if (is_null($kasir)) {
            return response()->json(['message' => 'Data Tidak Diterima'], 404);
        }

        $kasir->delete();

        return new apiResource($kasir, true, 'Data Item Berhasil Dihapus');
    }

}
