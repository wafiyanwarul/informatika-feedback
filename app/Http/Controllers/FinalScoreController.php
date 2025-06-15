<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FinalScore;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\PenilaianDosen;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FinalScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');

            $query = FinalScore::with(['dosen', 'mataKuliah']);

            if ($search) {
                $query->whereHas('dosen', function ($q) use ($search) {
                    $q->where('nama_dosen', 'like', "%{$search}%");
                })->orWhereHas('mataKuliah', function ($q) use ($search) {
                    $q->where('nama_mk', 'like', "%{$search}%");
                });
            }

            $finalScores = $query->orderBy('final_score', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Final scores retrieved successfully',
                'data' => $finalScores->items(),
                'pagination' => [
                    'current_page' => $finalScores->currentPage(),
                    'per_page' => $finalScores->perPage(),
                    'total' => $finalScores->total(),
                    'last_page' => $finalScores->lastPage(),
                    'from' => $finalScores->firstItem(),
                    'to' => $finalScores->lastItem(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve final scores',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'dosen_id' => 'required|exists:dosens,id',
                'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if combination already exists
            $existingFinalScore = FinalScore::byDosenAndMataKuliah(
                $request->dosen_id,
                $request->mata_kuliah_id
            )->first();

            if ($existingFinalScore) {
                return response()->json([
                    'success' => false,
                    'message' => 'Final score for this dosen and mata kuliah already exists',
                ], 409);
            }

            // Calculate final score
            $finalScore = FinalScore::calculateFinalScore(
                $request->dosen_id,
                $request->mata_kuliah_id
            );

            if ($finalScore === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'No evaluation data found for this dosen and mata kuliah',
                ], 404);
            }

            $newFinalScore = FinalScore::create([
                'dosen_id' => $request->dosen_id,
                'mata_kuliah_id' => $request->mata_kuliah_id,
                'final_score' => $finalScore,
            ]);

            $newFinalScore->load(['dosen', 'mataKuliah']);

            return response()->json([
                'success' => true,
                'message' => 'Final score created successfully',
                'data' => $newFinalScore
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create final score',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $finalScore = FinalScore::with(['dosen', 'mataKuliah'])->find($id);

            if (!$finalScore) {
                return response()->json([
                    'success' => false,
                    'message' => 'Final score not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Final score retrieved successfully',
                'data' => $finalScore
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve final score',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $finalScore = FinalScore::find($id);

            if (!$finalScore) {
                return response()->json([
                    'success' => false,
                    'message' => 'Final score not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'dosen_id' => 'sometimes|required|exists:dosens,id',
                'mata_kuliah_id' => 'sometimes|required|exists:mata_kuliahs,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $dosenId = $request->dosen_id ?? $finalScore->dosen_id;
            $mataKuliahId = $request->mata_kuliah_id ?? $finalScore->mata_kuliah_id;

            // Check if new combination already exists (excluding current record)
            if ($request->has('dosen_id') || $request->has('mata_kuliah_id')) {
                $existingFinalScore = FinalScore::byDosenAndMataKuliah($dosenId, $mataKuliahId)
                    ->where('id_final_scores', '!=', $id)
                    ->first();

                if ($existingFinalScore) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Final score for this dosen and mata kuliah already exists',
                    ], 409);
                }
            }

            // Recalculate final score
            $newFinalScore = FinalScore::calculateFinalScore($dosenId, $mataKuliahId);

            if ($newFinalScore === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'No evaluation data found for this dosen and mata kuliah',
                ], 404);
            }

            $finalScore->update([
                'dosen_id' => $dosenId,
                'mata_kuliah_id' => $mataKuliahId,
                'final_score' => $newFinalScore,
            ]);

            $finalScore->load(['dosen', 'mataKuliah']);

            return response()->json([
                'success' => true,
                'message' => 'Final score updated successfully',
                'data' => $finalScore
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update final score',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $finalScore = FinalScore::find($id);

            if (!$finalScore) {
                return response()->json([
                    'success' => false,
                    'message' => 'Final score not found',
                ], 404);
            }

            $finalScore->delete();

            return response()->json([
                'success' => true,
                'message' => 'Final score deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete final score',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recalculate all final scores
     */
    public function recalculateAll(): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Get all unique combinations of dosen_id and mk_id from penilaian_dosens
            $combinations = PenilaianDosen::select('dosen_id', 'mk_id')
                ->distinct()
                ->get();

            $updated = 0;
            $created = 0;

            foreach ($combinations as $combination) {
                $finalScore = FinalScore::updateOrCreateFinalScore(
                    $combination->dosen_id,
                    $combination->mk_id
                );

                if ($finalScore->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Final scores recalculated successfully',
                'data' => [
                    'created' => $created,
                    'updated' => $updated,
                    'total' => $created + $updated
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to recalculate final scores',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get final score by dosen and mata kuliah
     */
    public function getByDosenAndMataKuliah(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'dosen_id' => 'required|exists:dosens,id',
                'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $finalScore = FinalScore::with(['dosen', 'mataKuliah'])
                ->byDosenAndMataKuliah($request->dosen_id, $request->mata_kuliah_id)
                ->first();

            if (!$finalScore) {
                return response()->json([
                    'success' => false,
                    'message' => 'Final score not found for this dosen and mata kuliah',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Final score retrieved successfully',
                'data' => $finalScore
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve final score',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics of final scores
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_final_scores' => FinalScore::count(),
                'average_score' => round(FinalScore::avg('final_score'), 2),
                'highest_score' => FinalScore::max('final_score'),
                'lowest_score' => FinalScore::min('final_score'),
                'scores_by_range' => [
                    'excellent' => FinalScore::where('final_score', '>=', 4.5)->count(),
                    'good' => FinalScore::whereBetween('final_score', [3.5, 4.49])->count(),
                    'satisfactory' => FinalScore::whereBetween('final_score', [2.5, 3.49])->count(),
                    'needs_improvement' => FinalScore::where('final_score', '<', 2.5)->count(),
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $stats
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
