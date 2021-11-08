@extends('layouts.master')

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
@endpush
@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>KPI Perfomance {{ $nama_jabatan }}</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('jabatan') }}">Jabatan</a></li>
                <li class="breadcrumb-item"><a href="{{ route('kpiperformance', $id) }}">Manajemen KPI Perfomance
                        {{ $nama_jabatan }}</a></li>
                <li class="breadcrumb-item acrtive">Edit KPI Perfomance</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')

    <div class="card col-xl-12">
        <div class="card-header">
            <h4 class="card-title">Form Edit KPI Performance</h4>
        </div>
        <div class="card-body">
            <div class="basic-form">
                <form action="{{ route('update-kpiperformance', [$id,$id2]) }}" method="post">
                    @csrf
                    @method('put')
                    <div class="form-group">
                        <label>Kategori</label>
                        <select class="form-control" name="kategori">
                            <option value="">Pilih</option>
                            <option value="biaya"
                            @if ($kpips->kategori == "biaya")
                                selected
                            @endif
                            >Biaya</option>
                            <option value="kualitas"
                            @if ($kpips->kategori == "kualitas")
                                selected
                            @endif
                            >Kualitas</option>
                            <option value="kuantitas"
                            @if ($kpips->kategori == "kuantitas")
                                selected
                            @endif
                            >Kuantitas</option>
                            <option value="waktu"
                            @if ($kpips->kategori == "waktu")
                                selected
                            @endif
                            >Waktu</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipe Performance</label>
                        <select class="form-control" name="tipe_perform">
                            <option value="">Pilih</option>
                            <option value="maximum"
                            @if ($kpips->tipe_performance == "maximum")
                                selected
                            @endif
                            >Maximize</option>
                            <option value="minimize"
                            @if ($kpips->tipe_performance == "minimize")
                                selected
                            @endif
                            >Minimize</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Indikator KPI</label>
                        <input type="text" class="form-control" value="{{$kpips->indikator_kpi}}" placeholder="Masukkan Indikator KPI" name="indikator_kpi">
                    </div>
                    <div class="form-group">
                        <label>Definisi</label>
                        <textarea name="definisi" placeholder="Masukkan Definisi" class="form-control">{{$kpips->definisi}}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input type="text" id="satuan" class="form-control" value="{{$kpips->satuan}}" placeholder="Masukkan Satuan" name="satuan">
                    </div>
                    <label>Target</label>
                    <div class="input-group mb-3">
                        <input type="text" name="target" placeholder="Masukkan Target" value="{{$kpips->target}}" class="form-control">
                        <div class="input-group-append">
                            <span class="input-group-text" id="satuant">{{$kpips->satuan}}</span>
                        </div>
                    </div>
                    <label>Bobot</label>
                    <div class="input-group">
                        <input type="number" name="bobot" placeholder="Masukkan Bobot" value="{{$kpips->bobot}}" class="form-control">
                        <div class="input-group-append">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary mt-3" cl>Simpan</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('custom-script')
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script>
        let satuan = document.querySelector('#satuan');
        satuan.addEventListener('keyup', function() {
            let textEntered = satuan.value
            let targetSatuan = document.querySelector('#satuant')
            targetSatuan.innerText = textEntered;
        })
    </script>
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
