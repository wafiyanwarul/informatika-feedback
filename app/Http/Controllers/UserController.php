<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // insert user data to users table
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        // Jika ada password, hash; jika tidak, simpan null
        $data['password'] = isset($data['password']) ? Hash::make($data['password']) : null;

        $user = User::create($data);

        return response()->json([
            'status' => 201,
            'message' => 'User berhasil didaftarkan',
            'data' => $user
        ], 201);
    }

    // Get all users
    public function index()
    {
        $users = User::all();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mendapatkan seluruh data User',
            'data' => $users,
        ], 200);
    }

    // Get single user
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mendapatkan detail User',
            'data' => $user,
        ], 200);
    }

    // Get user by email
    public function getByEmail($email)
    {
        try {
            // Validasi format email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Format email tidak valid',
                ], 400);
            }

            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'User dengan email tersebut tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Berhasil mendapatkan data User berdasarkan email',
                'data' => $user,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan server saat mencari user berdasarkan email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get user by google_id
    public function getByGoogleId($google_id)
    {
        try {
            // Validasi google_id tidak boleh kosong
            if (empty($google_id)) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Google ID tidak boleh kosong',
                ], 400);
            }

            $user = User::where('google_id', $google_id)->first();

            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'User dengan Google ID tersebut tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Berhasil mendapatkan data User berdasarkan Google ID',
                'data' => $user,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan server saat mencari user berdasarkan Google ID',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update user's name only
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        $user->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil memperbarui nama User',
            'data' => $user,
        ], 200);
    }

    // Delete user
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil menghapus User',
        ], 200);
    }
}
