@extends('layouts.master')

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
@endpush
@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Review Pengajuan Banding</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('banding-penilaian') }}">Banding Penilaian</a></li>
                <li class="breadcrumb-item active">Review Pengajuan - {{ $pegawai->npk }} - {{ $pegawai->nama }}</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <div class="card col-xl-12">
        <div class="card-header">
            <h4 class="card-title">Form Review Pengajuan Banding</h4>
        </div>
        <div class="card-body">
            <div class="basic-form">
                <div class="form-group">
                    <label>Alasan Komplain</label>
                    <div class="card border col-xl-12">
                        <div class="card-body">
                            {{$banding[0]->alasan}}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Bukti pendukung</label>
                    <div class="card border col-xl-12">
                        <div class="card-body">
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe class="embed-responsive-item"src="{{asset('storage/'.$banding[0]->bukti)}}" allowfullscreen></iframe>
                              </div>
                        </div>
                    </div>
                </div>
                <form action="{{route('bp-tolak-pengajuan',$id)}}" method="post">
                    @csrf
                    @method('put')
                    <div class="form-group">
                        <label>Tulis Alasan tidak setuju</label>
                        <textarea  class="form-control" name="alasan_tolak" cols="30" rows="10"></textarea>
                    </div>
                    <button type="submit" class="btn btn-sm btn-danger mt-3" cl>Tolak Banding</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('custom-script')
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
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
