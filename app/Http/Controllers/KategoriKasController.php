<?php

namespace App\Http\Controllers;

use App\Models\KategoriKas;
use Illuminate\Http\Request;

class KategoriKasController extends Controller
{
    public function index()
    {
        $kategori = KategoriKas::latest()->paginate(10);
        $title = 'Data Kategori Kas';
        return view('admin.kategori_kas', compact('kategori', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:100',
        ]);

        KategoriKas::create($request->all());

        return redirect()->back()->with('success', 'Kategori kas berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:100',
        ]);

        $kategori = KategoriKas::findOrFail($id);
        $kategori->update($request->all());

        return redirect()->back()->with('success', 'Kategori kas berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kategori = KategoriKas::findOrFail($id);
        $kategori->delete();

        return redirect()->back()->with('success', 'Kategori kas berhasil dihapus');
    }
}