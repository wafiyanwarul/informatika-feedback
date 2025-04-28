<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
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
