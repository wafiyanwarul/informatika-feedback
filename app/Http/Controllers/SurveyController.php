<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\KategoriSurvey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SurveyController extends Controller
{
    // GET: /api/surveys
    public function index()
    {
        $surveys = Survey::with('kategori')->get();
        return response()->json([
            'status_code' => Response::HTTP_OK,
            'message' => 'Berhasil mendapatkan seluruh data Survey',
            'data' => $surveys
        ], Response::HTTP_OK);
    }

    // POST: /api/surveys
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:100',
            'deskripsi' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_surveys,id'
        ]);

        $survey = Survey::create($request->only('judul', 'deskripsi', 'kategori_id'));

        return response()->json([
            'status_code' => Response::HTTP_CREATED,
            'message' => 'Survey berhasil ditambahkan',
            'data' => $survey
        ], Response::HTTP_CREATED);
    }

    // GET: /api/surveys/{id}
    public function show($id)
    {
        $survey = Survey::with('kategori')->find($id);
        if (!$survey) {
            return response()->json([
                'status_code' => Response::HTTP_NOT_FOUND,
                'message' => 'Survey tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'status_code' => Response::HTTP_OK,
            'message' => 'Berhasil mendapatkan data Survey',
            'data' => $survey
        ], Response::HTTP_OK);
    }

    // PUT/PATCH: /api/surveys/{id}
    public function update(Request $request, $id)
    {
        $survey = Survey::find($id);
        if (!$survey) {
            return response()->json([
                'status_code' => Response::HTTP_NOT_FOUND,
                'message' => 'Survey tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'judul' => 'required|string|max:100',
            'deskripsi' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_surveys,id'
        ]);

        $survey->update($request->only('judul', 'deskripsi', 'kategori_id'));

        return response()->json([
            'status_code' => Response::HTTP_OK,
            'message' => 'Survey berhasil diperbarui',
            'data' => $survey
        ], Response::HTTP_OK);
    }

    // DELETE: /api/surveys/{id}
    public function destroy($id)
    {
        $survey = Survey::find($id);
        if (!$survey) {
            return response()->json([
                'status_code' => Response::HTTP_NOT_FOUND,
                'message' => 'Survey tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        $survey->delete();

        return response()->json([
            'status_code' => Response::HTTP_OK,
            'message' => 'Survey berhasil dihapus'
        ], Response::HTTP_OK);
    }
}
