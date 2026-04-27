@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h3 class="text-black text-capitalize ps-3">Konfigurasi Profil CV / Invoice</h3>
                </div>
            </div>

            <div class="card-body px-0 pb-2">
                @if(session('success'))
                    <div class="alert alert-success text-white mx-3">{{ session('success') }}</div>
                @endif

                <form action="{{ route('konfigurasi.update') }}" method="POST" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-body p-4">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label font-weight-bold">Nama CV</label>
                                    <input type="text" name="nama_cv" value="{{ old('nama_cv', $profile->nama_cv) }}" 
                                           class="form-control border p-2" required>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label font-weight-bold">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $profile->email) }}" 
                                           class="form-control border p-2" required>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label font-weight-bold">Telepon</label>
                                    <input type="text" name="telepon" value="{{ old('telepon', $profile->telepon) }}" 
                                           class="form-control border p-2" required>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label font-weight-bold">Logo Perusahaan</label>
                                    <input type="file" name="logo" id="logoInput" class="form-control border p-2" accept="image/*">
                                    
                                    <div class="mt-3">
                                        <label class="d-block text-xs font-weight-bold text-uppercase text-muted">Preview Logo:</label>
                                        <img id="logoPreview" 
                                        src="{{ $profile->logo ? url('storage/' . $profile->logo) : asset('assets/img/placeholder.png') }}" 
                                        alt="Preview" 
                                        style="max-height: 100px; border-radius: 8px; border: 1px solid #dee2e6;" 
                                        class="p-1">
                                    </div>
                                </div>

                                <div class="col-md-12 mb-4">
                                    <label class="form-label font-weight-bold">Alamat Lengkap</label>
                                    <textarea id="alamat" name="alamat" rows="4" 
                                              class="form-control border p-2">{{ old('alamat', $profile->alamat) }}</textarea>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary shadow-primary">Simpan Perubahan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview Gambar saat Pilih File
    document.getElementById('logoInput').onchange = function (evt) {
        const [file] = this.files
        if (file) {
            document.getElementById('logoPreview').src = URL.createObjectURL(file)
        }
    }

    // CKEditor
    ClassicEditor
        .create(document.querySelector('#alamat'), {
            toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList', 'undo', 'redo']
        })
        .catch(error => console.error(error));
</script>
@endsection