<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyProfileController extends Controller
{
    public function index()
    {
        $profile = CompanyProfile::first() ?? new CompanyProfile();
        $title = 'Konfigurasi Profil';
        return view('admin.konfigurasi', compact('profile', 'title'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_cv' => 'required|max:100',
            'email'   => 'required|email|max:100',
            'telepon' => 'required|max:20',
            'alamat'  => 'required',
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'website' => 'nullable'
        ]);

        $profile = CompanyProfile::find(1);

        $data = [
            'nama_cv' => $request->nama_cv,
            'email'   => $request->email,
            'telepon' => $request->telepon,
            'alamat'  => $request->alamat,
            'website' => $request->website,
        ];

        // 🔥 Upload Logo (FIX UTAMA ADA DI SINI)
        if ($request->hasFile('logo')) {

            // Hapus logo lama
            if ($profile && $profile->logo && Storage::disk('public')->exists($profile->logo)) {
                Storage::disk('public')->delete($profile->logo);
            }

            // Simpan ke storage/app/public/logo
            $path = $request->file('logo')->store('logo', 'public');

            // Simpan full path (best practice)
            $data['logo'] = $path;
        }

        if ($profile) {
            $profile->update($data);
        } else {
            $data['id'] = 1;
            CompanyProfile::create($data);
        }

        return redirect()->back()->with('success', 'Konfigurasi profil berhasil diperbarui');
    }
}