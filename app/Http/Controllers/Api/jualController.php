<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kasir;
use App\Models\DetailJual;
use App\Models\Jual;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\apiResource;

class jualController extends Controller
{
    public function index(){
        $jual = Jual::with(['kasir', 'detailjual.barang'])->get();

        return new apiResource($jual, true, 'Data Berhasil Ditampilkan');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'no_bon' => 'required|string|unique:juals,no_bon',
            'diskon' => 'required|numeric',
            'bayar' => 'required|numeric',
            'kode_kasir' => 'required|exists:kasirs,kode_kasir',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'barang' => 'required|array',
            'barang.*.kode_barang' => 'required|exists:barangs,kode_barang',
            'barang.*.jumlah' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $total = 0;

        // Hitung total dari detail
        foreach ($request->barang as $item) {
            $barangData = Barang::where('kode_barang', $item['kode_barang'])->first();
            $subtotal = $barangData->harga * $item['jumlah'];
            $total += $subtotal;
        }

        // Hitung setelah diskon
        $totalAkhir = $total - $request->diskon;

        // Hitung kembalian
        $kembali = $request->bayar - $totalAkhir;

        // Simpan data jual
        $jual = Jual::create([
            'no_bon' => $request->no_bon,
            'total' => $totalAkhir,
            'diskon' => $request->diskon,
            'bayar' => $request->bayar,
            'kembali' => $kembali,
            'kode_kasir' => $request->kode_kasir,
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
        ]);

        // Simpan detail jual
        foreach ($request->barang as $item) {

            $barangData = Barang::where('kode_barang', $item['kode_barang'])->first();

            DetailJual::create([
                'no_bon' => $request->no_bon,
                'kode_barang' => $item['kode_barang'],
                'harga' => $barangData->harga,
                'jumlah' => $item['jumlah'],
            ]); 
        }

        return new apiResource($jual, true, 'Data Berhasil Ditambah');
    }

    public function show($id)
    {
        $jual = Jual::with(['kasir', 'detailjual.barang'])->find($id);

        return new apiResource($jual, true, 'Data Berhasil Ditampilkan');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'no_bon' => 'nullable|string',
            'kode_kasir' => 'nullable|string|exists:kasirs,kode_kasir',
            'diskon' => 'nullable|numeric',
            'bayar' => 'nullable|numeric',

            'barang' => 'nullable|array',
            'barang.*.kode_barang' => 'nullable|string|exists:barangs,kode_barang',
            'barang.*.jumlah' => 'nullable|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $jual = Jual::find($id);

        if (!$jual) {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }

        if ($request->has('barang')) {
            foreach ($request->barang as $item) {

                $barangData = Barang::where('kode_barang', $item['kode_barang'])->first();

                $detail = DetailJual::where('no_bon', $request->no_bon)
                    ->where('kode_barang', $item['kode_barang'])
                    ->first();

                if ($detail) {
                    // Update jika sudah ada
                    $detail->update([
                        'jumlah' => $item['jumlah'],
                        'harga' => $barangData->harga
                    ]);
                } else {
                    // Tambah jika belum ada
                    DetailJual::create([
                        'no_bon' => $request->no_bon,
                        'kode_barang' => $item['kode_barang'],
                        'harga' => $barangData->harga,
                        'jumlah' => $item['jumlah'],
                    ]);
                }
            }
        }

        $total = DetailJual::where('no_bon', $request->no_bon)
            ->selectRaw('SUM(harga * jumlah) as total')
            ->value('total');

        $diskon = $request->diskon ?? 0;
        $bayar = $request->bayar ?? 0;

        $kembali = $request->bayar - ($total - $request->diskon);

        // 4. Update tabel jual
        $jual->update([
            'no_bon' => $request->no_bon ?? $jual->no_bon,
            'total' => $total,
            'diskon' => $diskon,
            'bayar' => $bayar,
            'kembali' => $kembali,
            'kode_kasir' => $request->kode_kasir ?? $jual->kode_kasir,
        ]);

        return new apiResource($jual, true, 'Data Berhasil Diupdate');

    }

    public function destroy($id)
    {
        $jual = Jual::find($id);

        if (!$jual) {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }

        $jual->delete();

        return new apiResource(null, true, 'Transaksi Berhasil Dihapus');
    }

}
