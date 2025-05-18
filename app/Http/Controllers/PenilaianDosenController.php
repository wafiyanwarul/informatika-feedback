<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenilaianDosen;
use App\Models\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenilaianDosenController extends Controller
{
    public function index()
    {
        try {
            $data = PenilaianDosen::with(['mahasiswa', 'dosen', 'mataKuliah', 'survey'])->get();

            return response()->json([
                'status' => 'success',
                'message' => 'List data penilaian dosen berhasil diambil',
                'data' => $data
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data penilaian dosen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:users,id',
            'dosen_id' => 'required|exists:dosens,id',
            'mk_id' => 'required|exists:mata_kuliahs,id',
            'survey_id' => 'required|exists:surveys,id',
        ]);

        try {
            // Ambil semua response dari mahasiswa tersebut untuk survey dan yang tipe pertanyaannya 'rating'
            $responses = Response::where('user_id', $request->mahasiswa_id)
                ->where('survey_id', $request->survey_id)
                ->whereHas('question', function ($query) {
                    $query->where('tipe', 'rating');
                })->pluck('nilai');

            if ($responses->count() == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ditemukan data nilai untuk user ini pada survey tersebut'
                ], 400);
            }

            $averageScore = round($responses->avg(), 2);

            $penilaian = PenilaianDosen::create([
                'mahasiswa_id' => $request->mahasiswa_id,
                'dosen_id' => $request->dosen_id,
                'mk_id' => $request->mk_id,
                'survey_id' => $request->survey_id,
                'nilai' => $averageScore
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data penilaian dosen berhasil disimpan',
                'data' => $penilaian
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data penilaian dosen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = PenilaianDosen::with(['mahasiswa', 'dosen', 'mataKuliah', 'survey'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Detail data penilaian dosen ditemukan',
                'data' => $data
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Temukan data penilaian berdasarkan ID
            $penilaian = PenilaianDosen::find($id);

            if (!$penilaian) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Penilaian dosen tidak ditemukan'
                ], 404);
            }

            // Hitung ulang rata-rata nilai dari tabel responses
            $responses = Response::where('user_id', $penilaian->mahasiswa_id)
                ->where('survey_id', $penilaian->survey_id)
                ->whereNotNull('nilai')
                ->get();

            if ($responses->count() === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ditemukan data response dengan nilai untuk dihitung ulang'
                ], 400);
            }

            $totalNilai = $responses->sum('nilai');
            $rataRata = round($totalNilai / $responses->count(), 2);

            // Update nilai
            $penilaian->nilai = $rataRata;
            $penilaian->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data penilaian berhasil diperbarui berdasarkan response terbaru',
                'data' => $penilaian
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data penilaian',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $data = PenilaianDosen::findOrFail($id);
            $data->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data penilaian dosen berhasil dihapus'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
