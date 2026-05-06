<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        $title = 'Data Supplier';
        return view('admin.supplier', compact('suppliers', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:150',
            'telp' => 'nullable|max:20',
        ]);

        Supplier::create($request->all());

        return redirect()->back()->with('success', 'Supplier berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:150',
            'telp' => 'nullable|max:20',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->all());

        return redirect()->back()->with('success', 'Supplier berhasil diperbarui');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete(); 

        return redirect()->back()->with('success', 'Supplier berhasil dinonaktifkan (Data histori tetap aman)');
    }

    public function transaksi(Request $request, $id)
    {
        $supplier = Supplier::withTrashed()->findOrFail($id);
        $search = $request->get('search');

        $pembelian = \App\Models\Pembelian::select('id', 'supplier_id', 'tanggal', 'total', 'status_pembayaran')
            ->with([
                'supplier' => function ($q) {
                    $q->withTrashed();
                },
                'detail'
            ])
            ->where('supplier_id', $id)
            ->when($search, function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%");
            })
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10);

        $title = 'Histori Transaksi: ' . $supplier->nama;
        
        return view('admin.supplier_transaksi', compact('supplier', 'pembelian', 'title'));
    }
}