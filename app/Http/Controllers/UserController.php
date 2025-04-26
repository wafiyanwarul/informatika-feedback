<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
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
}
