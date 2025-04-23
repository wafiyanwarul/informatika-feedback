<?php

namespace App\Http\Controllers;

use App\Models\BobotNilai;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BobotNilaiController extends Controller
{
    /**
     * Tampilkan semua bobot nilai.
     */
    public function index()
    {
        $bobotNilais = BobotNilai::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil data bobot nilai',
            'data' => $bobotNilais
        ], Response::HTTP_OK);
    }

    /**
     * Simpan bobot nilai baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:50',
            'skor' => 'required|integer|between:1,5',
        ]);

        $bobotNilai = BobotNilai::create($request->only(['deskripsi', 'skor']));

        return response()->json([
            'status' => 'success',
            'message' => 'Bobot nilai berhasil ditambahkan',
            'data' => $bobotNilai
        ], Response::HTTP_CREATED);
    }

    /**
     * Tampilkan bobot nilai berdasarkan ID.
     */
    public function show($id)
    {
        $bobotNilai = BobotNilai::find($id);
        if (!$bobotNilai) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bobot nilai tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil data bobot nilai',
            'data' => $bobotNilai
        ], Response::HTTP_OK);
    }

    /**
     * Update bobot nilai berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $bobotNilai = BobotNilai::find($id);
        if (!$bobotNilai) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bobot nilai tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'deskripsi' => 'required|string|max:50',
            'skor' => 'required|integer|between:1,5',
        ]);

        $bobotNilai->update($request->only(['deskripsi', 'skor']));

        return response()->json([
            'status' => 'success',
            'message' => 'Bobot nilai berhasil diperbarui',
            'data' => $bobotNilai
        ], Response::HTTP_OK);
    }

    /**
     * Hapus bobot nilai berdasarkan ID.
     */
    public function destroy($id)
    {
        $bobotNilai = BobotNilai::find($id);
        if (!$bobotNilai) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bobot nilai tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        $bobotNilai->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Bobot nilai berhasil dihapus'
        ], Response::HTTP_OK);
    }
}
