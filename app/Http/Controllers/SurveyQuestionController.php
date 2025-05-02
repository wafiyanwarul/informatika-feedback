<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SurveyQuestion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class SurveyQuestionController extends Controller
{
    /**
     * Menampilkan semua pertanyaan dari survey tertentu
     */
    public function index($survey_id)
    {
        try {
            $questions = SurveyQuestion::where('survey_id', $survey_id)->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar pertanyaan berhasil diambil.',
                'data' => $questions
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menambahkan pertanyaan baru ke survey
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'survey_id' => 'required|exists:surveys,id',
                'pertanyaan' => 'required|string',
                'tipe' => 'required|in:rating,kritik_saran'
            ]);

            $question = SurveyQuestion::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil ditambahkan.',
                'data' => $question
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pertanyaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate pertanyaan berdasarkan ID
     */
    public function update(Request $request, $id)
    {
        try {
            $question = SurveyQuestion::findOrFail($id);

            $validated = $request->validate([
                'pertanyaan' => 'sometimes|string',
                'tipe' => 'sometimes|in:rating,kritik_saran'
            ]);

            $question->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil diperbarui.',
                'data' => $question
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pertanyaan tidak ditemukan.',
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pertanyaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus pertanyaan
     */
    public function destroy($id)
    {
        try {
            $question = SurveyQuestion::findOrFail($id);
            $question->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil dihapus.'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pertanyaan tidak ditemukan.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pertanyaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
