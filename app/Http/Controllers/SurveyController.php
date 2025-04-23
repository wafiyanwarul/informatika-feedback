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
}
