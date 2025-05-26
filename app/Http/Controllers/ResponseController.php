<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Response;
use App\Models\SurveyQuestion;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ResponseController extends Controller
{
    /**
     * Display a listing of the responses.
     */
    public function index()
    {
        $responses = Response::with(['question', 'user', 'survey'])->get();

        return response()->json([
            'status' => 200,
            'message' => 'List of all responses retrieved successfully.',
            'data' => $responses
        ]);
    }

    /**
     * Store a newly created response in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'      => 'required|exists:users,id',
            'survey_id'    => 'required|exists:surveys,id',
            'question_id'  => 'required|exists:survey_questions,id',
            'nilai'        => 'nullable|integer|min:1|max:4',
            'kritik_saran' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Get question type
        $question = SurveyQuestion::findOrFail($request->question_id);

        // Logic: validation based on question type
        if ($question->tipe === 'rating') {
            if (is_null($request->nilai)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Nilai is required for rating-type questions.'
                ], 400);
            }
        } elseif ($question->tipe === 'kritik_saran') {
            if (is_null($request->kritik_saran)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Kritik/saran is required for kritik_saran-type questions.'
                ], 400);
            }
        }

        $response = Response::create([
            'user_id'      => $request->user_id,
            'survey_id'    => $request->survey_id,
            'question_id'  => $request->question_id,
            'nilai'        => $question->tipe === 'rating' ? $request->nilai : null,
            'kritik_saran' => $question->tipe === 'kritik_saran' ? $request->kritik_saran : null
        ]);

        return response()->json([
            'status'  => 201,
            'message' => 'Response created successfully.',
            'data'    => $response
        ]);
    }

    /**
     * Store multiple responses in storage.
     */
    public function multiInsert(Request $request)
    {
        try {
            // Cek apakah request memiliki key 'responses' atau langsung array
            if ($request->has('responses')) {
                $responses = $request->input('responses');
            } else {
                $responses = $request->all();
            }

            // Validasi bahwa responses adalah array dan tidak kosong
            if (!is_array($responses) || empty($responses)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Data responses harus berupa array dan tidak boleh kosong',
                ], 400);
            }
            $validatedResponses = [];
            $errors = [];

            // Validasi setiap response
            foreach ($responses as $index => $responseData) {
                $validator = Validator::make($responseData, [
                    'user_id'      => 'required|exists:users,id',
                    'survey_id'    => 'required|exists:surveys,id',
                    'question_id'  => 'required|exists:survey_questions,id',
                    'nilai'        => 'nullable|integer|min:1|max:4',
                    'kritik_saran' => 'nullable|string'
                ]);

                if ($validator->fails()) {
                    $errors["response_$index"] = $validator->errors();
                    continue;
                }

                try {
                    // Cek tipe question
                    $question = SurveyQuestion::findOrFail($responseData['question_id']);

                    // Validasi berdasarkan tipe pertanyaan
                    if ($question->tipe === 'rating') {
                        if (!isset($responseData['nilai']) || is_null($responseData['nilai'])) {
                            $errors["response_$index"]['nilai'] = ['Nilai is required for rating-type questions.'];
                            continue;
                        }
                    } elseif ($question->tipe === 'kritik_saran') {
                        if (!isset($responseData['kritik_saran']) || is_null($responseData['kritik_saran'])) {
                            $errors["response_$index"]['kritik_saran'] = ['Kritik/saran is required for kritik_saran-type questions.'];
                            continue;
                        }
                    }

                    // Siapkan data untuk insert
                    $validatedResponses[] = [
                        'user_id'      => $responseData['user_id'],
                        'survey_id'    => $responseData['survey_id'],
                        'question_id'  => $responseData['question_id'],
                        'nilai'        => $question->tipe === 'rating' ? $responseData['nilai'] : null,
                        'kritik_saran' => $question->tipe === 'kritik_saran' ? $responseData['kritik_saran'] : null,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    $errors["response_$index"]['question_id'] = ['Survey question not found.'];
                }
            }

            // Jika ada error validasi, kembalikan error
            if (!empty($errors)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validation failed for some responses',
                    'errors' => $errors,
                    'total_errors' => count($errors),
                    'total_valid' => count($validatedResponses)
                ], 422);
            }

            // Jika tidak ada data valid untuk diinsert
            if (empty($validatedResponses)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Tidak ada data response yang valid untuk diinsert',
                ], 400);
            }

            // Insert data menggunakan transaction
            DB::beginTransaction();

            $insertedResponses = collect($validatedResponses)->map(function ($responseData) {
                return Response::create($responseData);
            });

            DB::commit();

            return response()->json([
                'status' => 201,
                'message' => 'Multiple responses created successfully',
                'data' => $insertedResponses,
                'total_inserted' => $insertedResponses->count(),
                'summary' => [
                    'total_processed' => count($responses),
                    'total_success' => $insertedResponses->count(),
                    'total_failed' => count($responses) - $insertedResponses->count()
                ]
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan server saat melakukan multi insert responses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified response.
     */
    public function show($id)
    {
        $response = Response::with(['question', 'user', 'survey'])->find($id);

        if (!$response) {
            return response()->json([
                'status' => 404,
                'message' => 'Response not found.'
            ], 404);
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Response retrieved successfully.',
            'data'    => $response
        ]);
    }

    /**
     * Update the specified response in storage.
     */
    public function update(Request $request, $id)
    {
        $response = Response::find($id);

        if (!$response) {
            return response()->json([
                'status' => 404,
                'message' => 'Response not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nilai'        => 'nullable|integer|min:1|max:4',
            'kritik_saran' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 422,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $question = SurveyQuestion::findOrFail($response->question_id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Survey question not found.'
            ], 404);
        }

        if ($question->tipe === 'rating') {
            if (is_null($request->nilai)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Nilai is required for rating-type questions.'
                ], 400);
            }
        } elseif ($question->tipe === 'kritik_saran') {
            if (is_null($request->kritik_saran)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Kritik/saran is required for kritik_saran-type questions.'
                ], 400);
            }
        }

        $response->update([
            'nilai'        => $question->tipe === 'rating' ? $request->nilai : null,
            'kritik_saran' => $question->tipe === 'kritik_saran' ? $request->kritik_saran : null
        ]);

        return response()->json([
            'status'  => 200,
            'message' => 'Response updated successfully.',
            'data'    => $response
        ]);
    }

    /**
     * Remove the specified response from storage.
     */
    public function destroy($id)
    {
        $response = Response::find($id);

        if (!$response) {
            return response()->json([
                'status' => 404,
                'message' => 'Response not found.'
            ], 404);
        }

        $response->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Response deleted successfully.'
        ]);
    }
}
