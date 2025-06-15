<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosens';

    protected $fillable = ['nama_dosen', 'email']; // foto_profil sementara tidak digunakan

    /**
     * Relasi ke model MataKuliah melalui pivot table
     */
    public function mataKuliahs()
    {
        return $this->belongsToMany(MataKuliah::class, 'mata_kuliah_dosens', 'dosen_id', 'mata_kuliah_id');
    }

    /**
     * Relasi ke model PenilaianDosen
     */
    public function penilaianDosens()
    {
        return $this->hasMany(PenilaianDosen::class, 'dosen_id');
    }

    /**
     * Relasi ke model FinalScore
     */
    public function finalScores()
    {
        return $this->hasMany(FinalScore::class, 'dosen_id');
    }

    /**
     * Get average score for this dosen
     */
    public function getAverageScoreAttribute()
    {
        return $this->finalScores()->avg('final_score');
    }

    /**
     * Get total evaluations count
     */
    public function getTotalEvaluationsAttribute()
    {
        return $this->penilaianDosens()->count();
    }
}
