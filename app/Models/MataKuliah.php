<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliahs';
    
    protected $fillable = ['nama_mk', 'sks'];

    /**
     * Relasi ke model MataKuliahDosen
     */
    public function dosens()
    {
        return $this->belongsToMany(Dosen::class, 'mata_kuliah_dosens', 'mata_kuliah_id', 'dosen_id');
    }
}
