<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users (manager only or by role).
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('no_tlp', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = $request->input('per_page', 15);
        $users = $request->has('all') && $request->boolean('all')
            ? $query->latest('id_user')->get()
            : $query->latest('id_user')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Data pengguna berhasil diambil',
            'data' => $users
        ], 200);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|string|in:manajer,petugas_distribusi,pembesaran,pembibitan,pekerja',
            'no_tlp'   => 'nullable|string|max:20',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna berhasil dibuat',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail pengguna berhasil diambil',
            'data' => $user
        ], 200);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama'     => 'sometimes|required|string|max:255',
            'email'    => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id_user, 'id_user')],
            'password' => 'nullable|string|min:6',
            'role'     => 'sometimes|required|string|in:manajer,petugas_distribusi,pembesaran,pembibitan,pekerja',
            'no_tlp'   => 'nullable|string|max:20',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data pengguna berhasil diperbarui',
            'data' => $user
        ], 200);
    }

    /**
     * Update current authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'nama'     => 'sometimes|required|string|max:255',
            'email'    => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id_user, 'id_user')],
            'no_tlp'   => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
            'data' => $user
        ], 200);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna berhasil dihapus'
        ], 200);
    }
}
