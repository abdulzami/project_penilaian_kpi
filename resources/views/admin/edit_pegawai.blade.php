@extends('layouts.master')

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
@endpush
@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Edit Pegawai</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('pegawai') }}">Pegawai</a></li>
                <li class="breadcrumb-item active">Edit Pegawai</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <div class="card col-xl-6">
        <div class="card-header">
            <h4 class="card-title">Form Edit Pegawai</h4>
        </div>
        <div class="card-body">
            <div class="basic-form">
                <form action="{{ route('update-pegawai',$id) }}" method="post">
                    @csrf
                    @method('put')
                    <div class="form-group">
                        <label>NPK</label>
                        <input type="text" value="{{ $pegawais[0]->npk }}" name="npk" class="form-control"
                            placeholder="Masukkan NPK">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" value="{{ $pegawais[0]->nama }}" name="nama" class="form-control"
                            placeholder="Masukkan Nama">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="{{ $pegawais[0]->email }}" name="email" class="form-control"
                            placeholder="Masukkan Email">
                    </div>
                    <div class="form-group">
                        <label>Jabatan</label>
                        <select id="single-select" name="jabatan">
                            <option value="">Pilih</option>
                            @foreach ($jabatans as $jabatan)
                                <option value="{{ $hash->encode($jabatan->id_jabatan) }}"
                                    @if ($hash->encode($pegawais[0]->id_jabatan) == $hash->encode($jabatan->id_jabatan))
                                        selected
                                    @endif
                                    >{{ $jabatan->nama_jabatan }}
                                    {{ $jabatan->nama_struktural }} {{ $jabatan->nama_bidang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary mt-3">Simpan</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('custom-script')
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/select2-init.js') }}"></script>
    @if (session('success'))
        <script>
            toastr.success('{{ session('success') }}', 'Sukses', {
                positionClass: "toast-top-full-width",
                timeOut: 5e3,
                closeButton: !0,
                debug: !1,
                newestOnTop: !0,
                progressBar: !0,
                preventDuplicates: !0,
                onclick: null,
                showDuration: "300",
                hideDuration: "1000",
                extendedTimeOut: "1000",
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut",
                tapToDismiss: !1
            });
        </script>
    @endif
    @if (session('gagal'))
        <script>
            toastr.error('{{ session('gagal') }}', 'Gagal', {
                positionClass: "toast-top-full-width",
                timeOut: 5e3,
                closeButton: !0,
                debug: !1,
                newestOnTop: !0,
                progressBar: !0,
                preventDuplicates: !0,
                onclick: null,
                showDuration: "300",
                hideDuration: "1000",
                extendedTimeOut: "1000",
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut",
                tapToDismiss: !1
            });
        </script>
    @endif
    @if ($errors->any())
        <script>
            let errornya = [
                @foreach ($errors->all() as $error)
                    [ "{{ $error }}" ],
                @endforeach
            ];
            errornya.forEach(function(error) {
                toastr.warning(error, 'Kesalahan', {
                    positionClass: "toast-top-full-width",
                    timeOut: 5e3,
                    closeButton: !0,
                    debug: !1,
                    newestOnTop: !0,
                    progressBar: !0,
                    preventDuplicates: !0,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                    tapToDismiss: !1
                });
            });
        </script>
    @endif
@endpush
