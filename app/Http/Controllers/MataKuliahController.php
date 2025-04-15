<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MataKuliahController extends Controller
{
    /**
     * Tampilkan semua mata kuliah.
     */
    public function index()
    {
        $mataKuliahs = MataKuliah::with('dosen')->get();
        return response()->json([
            'message' => 'Berhasil mengambil data mata kuliah',
            'data' => $mataKuliahs
        ], Response::HTTP_OK);
    }

    /**
     * Simpan mata kuliah baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_mk' => 'required|string|max:100',
            'dosen_id' => 'required|exists:dosens,id',
        ]);

        $mataKuliah = MataKuliah::create([
            'nama_mk' => $request->nama_mk,
            'dosen_id' => $request->dosen_id,
        ]);

        return response()->json([
            'message' => 'Mata kuliah berhasil ditambahkan',
            'data' => $mataKuliah
        ], Response::HTTP_CREATED);
    }

    /**
     * Tampilkan mata kuliah berdasarkan ID.
     */
    public function show($id)
    {
        $mataKuliah = MataKuliah::with('dosen')->find($id);
        if (!$mataKuliah) {
            return response()->json([
                'message' => 'Mata kuliah tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Berhasil mengambil data mata kuliah',
            'data' => $mataKuliah
        ], Response::HTTP_OK);
    }

    /**
     * Update mata kuliah berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $mataKuliah = MataKuliah::find($id);
        if (!$mataKuliah) {
            return response()->json([
                'message' => 'Mata kuliah tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'nama_mk' => 'required|string|max:100',
            'dosen_id' => 'required|exists:dosens,id',
        ]);

        $mataKuliah->update([
            'nama_mk' => $request->nama_mk,
            'dosen_id' => $request->dosen_id,
        ]);

        return response()->json([
            'message' => 'Mata kuliah berhasil diperbarui',
            'data' => $mataKuliah
        ], Response::HTTP_OK);
    }

    // get mata kuliah by name
    public function searchByName($nama_mk)
    {
        $mataKuliahs = MataKuliah::with('dosen')
            ->where('nama_mk', 'like', "%$nama_mk%")
            ->get();

        if ($mataKuliahs->isEmpty()) {
            return response()->json([
                'message' => 'Mata kuliah tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Berhasil mengambil data mata kuliah',
            'data' => $mataKuliahs
        ], Response::HTTP_OK);
    }


    /**
     * Hapus mata kuliah berdasarkan ID.
     */
    public function destroy($id)
    {
        $mataKuliah = MataKuliah::find($id);
        if (!$mataKuliah) {
            return response()->json([
                'message' => 'Mata kuliah tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        $mataKuliah->delete();
        return response()->json([
            'message' => 'Mata kuliah berhasil dihapus'
        ], Response::HTTP_OK);
    }

    /**
     * Multi-insert mata kuliah.
     */
    public function multiInsert(Request $request)
    {
        $request->validate([
            'mata_kuliahs' => 'required|array',
            'mata_kuliahs.*.nama_mk' => 'required|string|max:100',
            'mata_kuliahs.*.dosen_id' => 'required|exists:dosens,id',
        ]);

        $data = [];
        foreach ($request->mata_kuliahs as $mataKuliah) {
            $data[] = [
                'nama_mk' => $mataKuliah['nama_mk'],
                'dosen_id' => $mataKuliah['dosen_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        MataKuliah::insert($data);

        return response()->json([
            'message' => count($data) . ' mata kuliah berhasil ditambahkan',
            'data' => $data
        ], Response::HTTP_CREATED);
    }
}
