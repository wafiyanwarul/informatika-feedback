<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MataKuliahController extends Controller
{
    public function index()
    {
        try {
            $data = MataKuliah::all();

            return response()->json([
                'status' => 200,
                'message' => 'Daftar Mata Kuliah berhasil diambil.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function searchByName($name)
    {
        try {
            $data = MataKuliah::where('nama_mk', 'LIKE', "%$name%")->get();

            return response()->json([
                'status' => 200,
                'message' => 'Hasil pencarian mata kuliah.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat mencari data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function multiInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mata_kuliahs' => 'required|array',
            'mata_kuliahs.*.nama_mk' => 'required|string|max:100',
            'mata_kuliahs.*.sks' => 'required|integer|min:1|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $timestamp = now();
            $mataKuliahData = collect($request->mata_kuliahs)->map(function ($item) use ($timestamp) {
                return array_merge($item, [
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            })->toArray();

            MataKuliah::insert($mataKuliahData);

            return response()->json([
                'status' => 201,
                'message' => 'Mata Kuliah berhasil ditambahkan secara massal.',
                'data' => $mataKuliahData
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat menambahkan data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_mk' => 'required|string|max:100',
            'sks' => 'required|integer|min:1|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $mataKuliah = MataKuliah::create($request->all());

            return response()->json([
                'status' => 201,
                'message' => 'Mata Kuliah berhasil ditambahkan.',
                'data' => $mataKuliah
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat menambahkan data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $mataKuliah = MataKuliah::find($id);

            if (!$mataKuliah) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Mata Kuliah tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Detail Mata Kuliah berhasil diambil.',
                'data' => $mataKuliah
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_mk' => 'required|string|max:100',
            'sks' => 'required|integer|min:1|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $mataKuliah = MataKuliah::find($id);

            if (!$mataKuliah) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Mata Kuliah tidak ditemukan.'
                ], 404);
            }

            $mataKuliah->update($request->all());

            return response()->json([
                'status' => 200,
                'message' => 'Mata Kuliah berhasil diupdate.',
                'data' => $mataKuliah
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat mengupdate data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $mataKuliah = MataKuliah::find($id);

            if (!$mataKuliah) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Mata Kuliah tidak ditemukan.'
                ], 404);
            }

            $mataKuliah->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Mata Kuliah berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat menghapus data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
