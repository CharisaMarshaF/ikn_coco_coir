<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CompanyProfile;
use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::latest()->paginate(10);
        $title = 'Data Client';
        return view('admin.client', compact('clients', 'title'));
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

    public function history($id)
    {
        $client = Client::findOrFail($id);
        
        $penjualan = Penjualan::where('client_id', $id)
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get(); 

        $title = 'Histori Transaksi - ' . $client->nama;
        
        return view('admin.client_history', compact('client', 'penjualan', 'title'));
    }

    public function cetakHistory(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $client = Client::findOrFail($id);
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = Penjualan::where('client_id', $id)
            ->whereBetween('tanggal', [$start_date, $end_date])
            ->with(['detail.produk']) // Eager loading
            ->orderBy('tanggal', 'asc')
            ->get();

        $total_omzet = $data->sum('total');
        $konfigurasi = CompanyProfile::first();

        $pdf = Pdf::loadView('admin.laporan.client_history_pdf', [
            'client' => $client,
            'data' => $data,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_omzet' => $total_omzet,
            'konfigurasi' => $konfigurasi,
        ]);

        return $pdf->stream('Histori_Transaksi_' . $client->nama . '.pdf');
    }
}