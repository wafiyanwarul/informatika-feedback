<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RoleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ROLES CRUD ROUTES
Route::apiResource('roles', RoleController::class);
Route::get('roles/search/{nama}', [RoleController::class, 'search']); // search by name
// GET	        /api/roles	    index()	                Mengambil semua data role
// POST	        /api/roles	    store()	                Menambah data role baru
// GET	        /api/roles/{id}	show($id)	            Mengambil data role berdasarkan ID
// PUT/PATCH	/api/roles/{id}	update($request, $id)	Memperbarui data role berdasarkan ID
// DELETE	    /api/roles/{id}	destroy($id)	        Menghapus data role berdasarkan ID
