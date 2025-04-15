<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dosen;
use Illuminate\Http\Response;

class DosenController extends Controller
{
    /**
     * Tampilkan semua dosen.
     */
    public function index()
    {
        $dosens = Dosen::all();
        return response()->json([
            'message' => 'Berhasil mengambil data dosen',
            'data' => $dosens
        ], Response::HTTP_OK);
    }

    /**
     * Simpan dosen baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_dosen' => 'required|string|max:100',
            'email' => 'required|email|unique:dosens,email',
            // 'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $dosen = Dosen::create([
            'nama_dosen' => $request->nama_dosen,
            'email' => $request->email,
        ]);

        // Logika upload foto (sementara dikomentari)
        // if ($request->hasFile('foto_profil')) {
        //     $file = $request->file('foto_profil');
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     $file->storeAs('public/dosen_profiles', $filename);
        //     $dosen->foto_profil = $filename;
        //     $dosen->save();
        // }

        return response()->json([
            'message' => 'Dosen berhasil ditambahkan',
            'data' => $dosen
        ], Response::HTTP_CREATED);
    }

    /**
     * Simpan dosen baru - multiinsert (no image).
     */
    public function multiInsert(Request $request)
    {
        $request->validate([
            'dosens' => 'required|array',
            'dosens.*.nama_dosen' => 'required|string|max:100',
            'dosens.*.email' => 'required|email|unique:dosens,email',
        ]);

        $data = [];
        foreach ($request->dosens as $dosen) {
            $data[] = [
                'nama_dosen' => $dosen['nama_dosen'],
                'email' => $dosen['email'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Dosen::insert($data);

        return response()->json([
            'message' => count($data) . ' dosen berhasil ditambahkan',
            'data' => $data
        ], Response::HTTP_CREATED);
    }


    /**
     * Tampilkan dosen berdasarkan ID.
     */
    public function show($id)
    {
        $dosen = Dosen::find($id);
        if (!$dosen) {
            return response()->json([
                'message' => 'Dosen tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Berhasil mengambil data dosen',
            'data' => $dosen
        ], Response::HTTP_OK);
    }

    /**
     * Update dosen berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $dosen = Dosen::find($id);
        if (!$dosen) {
            return response()->json([
                'message' => 'Dosen tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'nama_dosen' => 'required|string|max:100',
            'email' => 'required|email|unique:dosens,email,' . $id,
            // 'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $dosen->update([
            'nama_dosen' => $request->nama_dosen,
            'email' => $request->email,
        ]);

        // Logika update foto (sementara dinonaktifkan)
        // if ($request->hasFile('foto_profil')) {
        //     $file = $request->file('foto_profil');
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     $file->storeAs('public/dosen_profiles', $filename);
        //     $dosen->foto_profil = $filename;
        //     $dosen->save();
        // }

        return response()->json([
            'message' => 'Dosen berhasil diperbarui',
            'data' => $dosen
        ], Response::HTTP_OK);
    }

    /**
     * Delete dosen berdasarkan ID.
     */
    public function destroy($id)
    {
        $dosen = Dosen::find($id);
        if (!$dosen) {
            return response()->json([
                'message' => 'Dosen tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        $dosen->delete();
        return response()->json([
            'message' => 'Dosen berhasil dihapus'
        ], Response::HTTP_OK);
    }
}
