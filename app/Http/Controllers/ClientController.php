<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::latest()->paginate(10);
        return view('admin.client', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:150',
            'telp' => 'nullable|max:20',
        ]);

        Client::create($request->all());

        return redirect()->back()->with('success', 'Client berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:150',
            'telp' => 'nullable|max:20',
        ]);

        $client = Client::findOrFail($id);
        $client->update($request->all());

        return redirect()->back()->with('success', 'Client berhasil diperbarui');
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return redirect()->back()->with('success', 'Client berhasil dihapus');
    }
}