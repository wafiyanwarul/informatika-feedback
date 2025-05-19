<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliahDosen extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah_dosens';

    protected $fillable = ['mata_kuliah_id', 'dosen_id'];

    /**
     * Relasi ke model MataKuliah
     */
    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    /**
     * Relasi ke model Dosen
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }
}
