<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSurvey extends Model
{
    use HasFactory;

    protected $table = 'kategori_surveys';

    protected $fillable = ['nama_kategori'];
}
