<?php

namespace App\Http\Controllers;

use App\Models\MataKuliahDosen;
use App\Models\MataKuliah;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MataKuliahDosenController extends Controller
{
    public function index()
    {
        try {
            $data = MataKuliahDosen::with(['mataKuliah', 'dosen'])->get();

            return response()->json([
                'status' => 200,
                'message' => 'Daftar Mata Kuliah Dosen berhasil diambil.',
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

    public function show($id)
    {
        try {
            $relasi = MataKuliahDosen::with(['mataKuliah', 'dosen'])->find($id);

            if (!$relasi) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Relasi tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Detail Relasi berhasil diambil.',
                'data' => $relasi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'dosen_id' => 'required|exists:dosens,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $relasi = MataKuliahDosen::create($request->all());

            return response()->json([
                'status' => 201,
                'message' => 'Relasi Mata Kuliah Dosen berhasil ditambahkan.',
                'data' => $relasi->load(['mataKuliah', 'dosen'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat menambahkan relasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function multiInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mata_kuliahs' => 'required|array',
            'mata_kuliahs.*.mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'mata_kuliahs.*.dosen_id' => 'required|exists:dosens,id'
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
            $data = collect($request->mata_kuliahs)->map(function ($item) use ($timestamp) {
                return array_merge($item, [
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);
            })->toArray();

            MataKuliahDosen::insert($data);

            $insertedIds = collect($data)->pluck('mata_kuliah_id')->toArray();
            $insertedRecords = MataKuliahDosen::whereIn('mata_kuliah_id', $insertedIds)->with(['mataKuliah', 'dosen'])->get();

            return response()->json([
                'status' => 201,
                'message' => 'Mata Kuliah Dosen berhasil ditambahkan secara massal.',
                'data' => $insertedRecords
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat menambahkan data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $relasi = MataKuliahDosen::find($id);

            if (!$relasi) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Relasi tidak ditemukan.'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
                'dosen_id' => 'required|exists:dosens,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors()
                ], 400);
            }

            $relasi->update($request->all());

            return response()->json([
                'status' => 200,
                'message' => 'Relasi Mata Kuliah Dosen berhasil diupdate.',
                'data' => $relasi->load(['mataKuliah', 'dosen'])
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
            $relasi = MataKuliahDosen::find($id);

            if (!$relasi) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Relasi tidak ditemukan.'
                ], 404);
            }

            $relasi->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Relasi Mata Kuliah Dosen berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat menghapus relasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
