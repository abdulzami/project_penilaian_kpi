@extends('layouts.master')

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
@endpush
@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Penilaian KPI Perilaku</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('belum-dinilai') }}">Penilaian Belum Dinilai</a></li>
                <li class="breadcrumb-item active">KPI Perilaku - {{$pegawai->npk}} - {{$pegawai->nama}}</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <div class="card col-xl-12">
        <div class="card-header">
            <h4 class="card-title">Form Penilaian KPI Performance</h4>
        </div>
        <div class="card-body">
            <div class="basic-form">
                <form action="{{route('belum-dinilai-kpi-perilaku-update',$id)}}" method="post">
                    @csrf
                    @method('put')
                    <div class="table-responsive">
                        <table class="table small">
                            <thead>
                                <tr>
                                    <th style="width: 100px">Nama KPI</th>
                                    <th>Ekselen (5)</th>
                                    <th>Baik (4)</th>
                                    <th>Cukup (3)</th>
                                    <th>Kurang (2)</th>
                                    <th>Kurang Sekali (1)</th>
                                    <th>Pilih Skala</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penilaianperilakus as $index => $perilaku)
                                    <tr>
                                        <td><strong>{{$perilaku->nama_kpi}}</strong></td>
                                        <td>{{$perilaku->ekselen}}</td>
                                        <td>{{$perilaku->baik}}</td>
                                        <td>{{$perilaku->cukup}}</td>
                                        <td>{{$perilaku->kurang}}</td>
                                        <td>{{$perilaku->kurang_sekali}}</td>
                                        <td>
                                            <select class="form-control" name="{{ $id }}{{ $hash->encode($perilaku->id_perilaku) }}">
                                                <option value="">Pilih</option>
                                                <option value="5"
                                                @if ($perilaku->nilai_perilaku == 5)
                                                    selected
                                                @endif
                                                >5</option>
                                                <option value="4"
                                                @if ($perilaku->nilai_perilaku == 4)
                                                    selected
                                                @endif
                                                >4</option>
                                                <option value="3"
                                                @if ($perilaku->nilai_perilaku == 3)
                                                    selected
                                                @endif
                                                >3</option>
                                                <option value="2"
                                                @if ($perilaku->nilai_perilaku == 2)
                                                    selected
                                                @endif
                                                >2</option>
                                                <option value="1"
                                                @if ($perilaku->nilai_perilaku == 1)
                                                    selected
                                                @endif
                                                >1</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <center><button type="submit" class="btn btn-sm btn-primary mt-3">Simpan</button></center>
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
