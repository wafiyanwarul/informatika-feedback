<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalScore extends Model
{
    use HasFactory;

    protected $table = 'final_scores';
    protected $primaryKey = 'id_final_scores';

    protected $fillable = [
        'dosen_id',
        'mata_kuliah_id',
        'final_score',
    ];

    protected $casts = [
        'final_score' => 'float',
    ];

    /**
     * Relasi ke model Dosen
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    /**
     * Relasi ke model MataKuliah
     */
    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    /**
     * Scope untuk mencari berdasarkan dosen dan mata kuliah
     */
    public function scopeByDosenAndMataKuliah($query, $dosenId, $mataKuliahId)
    {
        return $query->where('dosen_id', $dosenId)
                    ->where('mata_kuliah_id', $mataKuliahId);
    }

    /**
     * Hitung final score dari tabel penilaian_dosens
     */
    public static function calculateFinalScore($dosenId, $mataKuliahId)
    {
        $averageScore = PenilaianDosen::where('dosen_id', $dosenId)
            ->where('mk_id', $mataKuliahId)
            ->avg('nilai');

        return round($averageScore, 2);
    }

    /**
     * Update atau create final score
     */
    public static function updateOrCreateFinalScore($dosenId, $mataKuliahId)
    {
        $finalScore = self::calculateFinalScore($dosenId, $mataKuliahId);

        return self::updateOrCreate(
            [
                'dosen_id' => $dosenId,
                'mata_kuliah_id' => $mataKuliahId
            ],
            [
                'final_score' => $finalScore
            ]
        );
    }
}
