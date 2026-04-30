<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
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
            'email' => 'required|email|unique:users,email',
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
            'email' => 'required|email|unique:users,email,' . $id,
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
        User::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus');
    }
}
