<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // Hanya menampilkan user yang masih aktif
        $users = User::latest()->paginate(10);
        $title = 'Data User';
        return view('admin.users', compact('users', 'title'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        $request->validate([
            'name' => 'required',
            // Validasi email agar mengecek data yang di-soft delete juga
            'email' => [
                'required', 
                'email', 
                Rule::unique('users')->whereNull('deleted_at')
            ],
            'password' => 'required|min:6',
            'role' => 'required|in:admin,staff',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'email' => [
                'required', 
                'email', 
                Rule::unique('users')->ignore($id)->whereNull('deleted_at')
            ],
            'role' => 'required|in:admin,staff',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'User berhasil diperbarui');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        // PROTEKSI: Jangan biarkan admin menghapus dirinya sendiri
        if (auth()->id() == $id) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        $user = User::findOrFail($id);
        $user->delete(); // Ini melakukan Soft Delete

        return redirect()->back()->with('success', 'User berhasil dinonaktifkan.');
    }

    /**
     * Opsional: Fitur untuk melihat user yang sudah dihapus
     */
    public function trashed()
    {
        $users = User::onlyTrashed()->latest()->paginate(10);
        $title = 'Arsip User (Non-Aktif)';
        return view('admin.users_trashed', compact('users', 'title'));
    }
}