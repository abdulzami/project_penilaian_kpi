@extends('layouts.master')

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
@endpush
@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Penilaian KPI Performance</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('belum-dinilai') }}">Penilaian Belum Dinilai</a></li>
                <li class="breadcrumb-item active">KPI Performance - {{ $pegawai->npk }} - {{ $pegawai->nama }}</li>
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
                <form action="{{ route('belum-dinilai-kpi-performance-store', $id) }}" method="post">
                    @csrf
                    {{-- <div class="form-group">
                        <label>Nama Struktural</label>
                        <input type="text" name="nama_struktural" class="form-control"
                            placeholder="Masukkan nama struktural">
                    </div> --}}

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kategori</th>
                                    <th>Tipe Performance</th>
                                    <th>Indikator KPI</th>
                                    <th>Definisi</th>
                                    <th>Target</th>
                                    <th>Bobot</th>
                                    <th>Realisasi</th>
                                    <th>Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($kpiperformances->isEmpty())
                                    <tr>
                                        <td colspan="8" class="text-center">No data available in table</td>
                                    </tr>
                                @endif
                                @foreach ($kpiperformances as $index => $kpip)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $kpip->kategori }}</td>
                                        <td>{{ $kpip->tipe_performance }}</td>
                                        <td>{{ $kpip->indikator_kpi }}</td>
                                        <td>{{ $kpip->definisi }}</td>
                                        <td>{{ $kpip->target }} {{ $kpip->satuan }}</td>                                      
                                        <td>{{ $kpip->bobot }}%</td>
                                        <td>
                                            @if ($kpip->satuan == '%')
                                                <input type="number"
                                                    name="{{ $id }}{{ $hash->encode($kpip->id_performance) }}"
                                                    class="form-control">
                                            @else
                                                <input type="text"
                                                    name="{{ $id }}{{ $hash->encode($kpip->id_performance) }}"
                                                    class="form-control">
                                            @endif

                                        </td>
                                        <td>{{ $kpip->satuan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <center> <button type="submit" class="btn btn-sm btn-primary mt-3">Simpan</button> </center>
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
