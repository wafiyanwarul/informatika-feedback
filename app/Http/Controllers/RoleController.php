<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    /**
     * Tampilkan semua role.
     */
    public function index()
    {
        return response()->json([
            'message' => 'Successfully get all roles',
            'data' => Role::all()
        ], Response::HTTP_OK);
    }

    /**
     * Simpan role baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_role' => 'required|string|max:50|unique:roles,nama_role',
        ]);

        $role = Role::create([
            'nama_role' => $request->nama_role,
        ]);

        return response()->json(
            [
                'message' => 'Role created successfully',
                'data' => $role,
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * Tampilkan role berdasarkan ID.
     */
    public function show($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($role, Response::HTTP_OK);
    }

    /**
     * Update role berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'nama_role' => 'required|string|max:50|unique:roles,nama_role,' . $id,
        ]);

        $role->update([
            'nama_role' => $request->nama_role,
        ]);

        return response()->json([
            'message' => 'Role has been changed',
            'data' => $role
        ], Response::HTTP_OK);
    }

    /**
     * Hapus role berdasarkan ID.
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['message' => 'Role tidak ditemukan'], Response::HTTP_NOT_FOUND);
        }

        $role->delete();
        return response()->json(['message' => 'Role berhasil dihapus'], Response::HTTP_OK);
    }
}
