<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PenilaianDosen extends Model
{
    use HasFactory;

    protected $fillable = [
        'mahasiswa_id',
        'dosen_id',
        'mk_id',
        'survey_id',
        'nilai',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mk_id');
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Scope untuk filter berdasarkan dosen dan mata kuliah
     */
    public function scopeByDosenAndMataKuliah($query, $dosenId, $mataKuliahId)
    {
        return $query->where('dosen_id', $dosenId)
                    ->where('mk_id', $mataKuliahId);
    }

    /**
     * Scope untuk filter berdasarkan range nilai
     */
    public function scopeByScoreRange($query, $min, $max)
    {
        return $query->whereBetween('nilai', [$min, $max]);
    }

    /**
     * Get statistics for specific dosen and mata kuliah
     */
    public static function getStatistics($dosenId, $mataKuliahId)
    {
        $evaluations = static::byDosenAndMataKuliah($dosenId, $mataKuliahId)->get();

        if ($evaluations->isEmpty()) {
            return null;
        }

        return [
            'count' => $evaluations->count(),
            'average' => round($evaluations->avg('nilai'), 2),
            'min' => $evaluations->min('nilai'),
            'max' => $evaluations->max('nilai'),
            'distribution' => [
                'excellent' => $evaluations->where('nilai', '>=', 4.5)->count(),
                'good' => $evaluations->whereBetween('nilai', [3.5, 4.49])->count(),
                'satisfactory' => $evaluations->whereBetween('nilai', [2.5, 3.49])->count(),
                'needs_improvement' => $evaluations->where('nilai', '<', 2.5)->count(),
            ]
        ];
    }
}
