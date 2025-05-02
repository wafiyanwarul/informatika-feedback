<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\KategoriSurveyController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\BobotNilaiController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SurveyQuestionController;

// USER CRUD (AUTH-USER)
Route::post('/users', [UserController::class, 'store']);

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']); // Read all users
    Route::get('/{id}', [UserController::class, 'show']); // Read one user
    Route::put('/{id}', [UserController::class, 'update']); // Update user name
    Route::delete('/{id}', [UserController::class, 'destroy']); // Delete user
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// ROLES CRUD ROUTES
Route::get('roles/search/{nama}', [RoleController::class, 'search']); // search by name
Route::apiResource('roles', RoleController::class);
// GET	        /api/roles	    index()	                Mengambil semua data role
// POST	        /api/roles	    store()	                Menambah data role baru
// GET	        /api/roles/{id}	show($id)	            Mengambil data role berdasarkan ID
// PUT/PATCH	/api/roles/{id}	update($request, $id)	Memperbarui data role berdasarkan ID
// DELETE	    /api/roles/{id}	destroy($id)	        Menghapus data role berdasarkan ID

// KATEGORI SURVEY CRUD ROUTES
Route::apiResource('kategori-surveys', KategoriSurveyController::class);
// GET	        /api/kategori-surveys	    index()                 READ semua data
// POST	        /api/kategori-surveys	    store()                 CREATE data baru
// GET	        /api/kategori-surveys/{id}	show($id)	            READ berdasarkan ID
// PUT/PATCH	/api/kategori-surveys/{id}	update($request, $id)	UPDATE berdasarkan ID
// DELETE	    /api/kategori-surveys/{id}	destroy($id)	        DELETE berdasarkan ID

// DOSEN CRUD ROUTES
Route::apiResource('dosens', DosenController::class);
Route::post('dosens/multi-insert', [DosenController::class, 'multiInsert']); // multi-insert data
// GET	        /api/dosens	        index()                 READ semua data
// POST	        /api/dosens 	    store()                 CREATE data baru
// GET	        /api/dosens/{id}	show($id)	            READ berdasarkan ID
// PUT/PATCH	/api/dosens/{id}	update($request, $id)	UPDATE berdasarkan ID
// DELETE	    /api/dosens/{id}	destroy($id)	        DELETE berdasarkan ID

// MATA KULIAH CRUD ROUTES
Route::apiResource('mata-kuliahs', MataKuliahController::class);
Route::get('mata-kuliahs/search/{nama_mk}', [MataKuliahController::class, 'searchByName']);
Route::post('mata-kuliahs/multi-insert', [MataKuliahController::class, 'multiInsert']);
// GET	        /api/mata-kuliahs	    index()                 READ semua data
// POST	        /api/mata-kuliahs 	    store()                 CREATE data baru
// GET	        /api/mata-kuliahs/{id}	show($id)	            READ berdasarkan ID
// PUT/PATCH	/api/mata-kuliahs/{id}	update($request, $id)	UPDATE berdasarkan ID
// DELETE	    /api/mata-kuliahs/{id}	destroy($id)	        DELETE berdasarkan ID

// BOBOT NILAI CRUD ROUTES
Route::apiResource('bobot-nilais', BobotNilaiController::class);

// GET         /api/bobot-nilais           -> index()
// POST        /api/bobot-nilais           -> store()
// GET         /api/bobot-nilais/{id}      -> show()
// PUT/PATCH   /api/bobot-nilais/{id}      -> update()
// DELETE      /api/bobot-nilais/{id}      -> destroy()

Route::apiResource('surveys', SurveyController::class);

// SURVEY_QUESTIONS CRUD ROUTES
Route::get('/survey-questions/{survey_id}', [SurveyQuestionController::class, 'index']);
Route::post('/survey-questions', [SurveyQuestionController::class, 'store']);
Route::put('/survey-questions/{id}', [SurveyQuestionController::class, 'update']);
Route::delete('/survey-questions/{id}', [SurveyQuestionController::class, 'destroy']);


