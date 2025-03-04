<?php

namespace App\Http\Controllers;

use App\Models\KategoriSurvey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KategoriSurveyController extends Controller
{
    /**
     * Tampilkan semua kategori survei.
     */
    public function index()
    {
        $kategori = KategoriSurvey::all();
        return response()->json([
            'message' => 'Berhasil mengambil data kategori survei',
            'data' => $kategori
        ], Response::HTTP_OK);
    }

    /**
     * Simpan kategori survei baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_surveys,nama_kategori',
        ]);

        $kategori = KategoriSurvey::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return response()->json([
            'message' => 'Kategori survei berhasil ditambahkan',
            'data' => $kategori
        ], Response::HTTP_CREATED);
    }

    /**
     * Tampilkan kategori survei berdasarkan ID.
     */
    public function show($id)
    {
        $kategori = KategoriSurvey::find($id);
        if (!$kategori) {
            return response()->json([
                'message' => 'Kategori survei tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Berhasil mengambil data kategori survei',
            'data' => $kategori
        ], Response::HTTP_OK);
    }

    /**
     * Update kategori survei berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriSurvey::find($id);
        if (!$kategori) {
            return response()->json([
                'message' => 'Kategori survei tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_surveys,nama_kategori,' . $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return response()->json([
            'message' => 'Kategori survei berhasil diperbarui',
            'data' => $kategori
        ], Response::HTTP_OK);
    }

    /**
     * Hapus kategori survei berdasarkan ID.
     */
    public function destroy($id)
    {
        $kategori = KategoriSurvey::find($id);
        if (!$kategori) {
            return response()->json([
                'message' => 'Kategori survei tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        $kategori->delete();
        return response()->json([
            'message' => 'Kategori survei berhasil dihapus'
        ], Response::HTTP_OK);
    }
}

