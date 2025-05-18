<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Response;
use App\Models\SurveyQuestion;
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

        $question = SurveyQuestion::find($response->question_id);

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
