@extends('layouts.master')

@push('custom-css')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
@endpush

@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Penilaian</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Banding Penilaian</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Banding Penilaian</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example4" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NPK</th>
                                    <th>Nama Pegawai</th>
                                    <th>Jabatan</th>
                                    <th>Total Sebelum Pengurangan</th>
                                    <th>Total Sebelum Banding</th>
                                    <th>Total</th>
                                    <th style="width: 100px">Capaian</th>
                                    <th>Status Banding</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dinilais as $index => $dinilai)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $dinilai->npk }}</td>
                                        <td>{{ $dinilai->nama }}</td>
                                        <td>{{ $dinilai->nama_jabatan }} {{ $dinilai->nama_struktural }}
                                            {{ $dinilai->nama_bidang }}</td>
                                        @if ($dinilai->total_sebelum_pengurangan == 'no')
                                            <td>
                                                <div class="badge badge-xs light badge-info">Tidak ada pengurangan</div>
                                            </td>
                                        @else
                                            <td>{{ $dinilai->total_sebelum_pengurangan }}</td>
                                        @endif

                                        @if ($dinilai->total_sebelum_banding == 'ditolak')
                                            <td>
                                                <div class="badge badge-xs light badge-danger">Banding ditolak</div>
                                            </td>
                                        @elseif($dinilai->total_sebelum_banding == 'belum_diajukan')
                                        <td>
                                            <div class="badge badge-xs light badge-info">Belum diajukan</div>
                                        </td>
                                        @elseif($dinilai->total_sebelum_banding == 'proses')
                                        <td>
                                            <div class="badge badge-xs light badge-info">Proses</div>
                                        </td>
                                        @elseif($dinilai->total_sebelum_banding == 'tidak_banding')
                                            <td>
                                                <div class="badge badge-xs light badge-info">Tidak melakukan banding</div>
                                            </td>
                                        @else
                                            <td>{{ $dinilai->total_sebelum_banding }}</td>
                                        @endif
                                        <td>{{ $dinilai->total }} </td>
                                        {{-- <td></td> --}}
                                        <td>{{ $dinilai->capaian }}</td>
                                        <td>
                                            @if ($dinilai->status_banding == 'diterima_mv')
                                                <div class="badge badge-xs light badge-success">Setuju</div>
                                                <div class="badge badge-xs light badge-info">Menunggu Verifikasi</div>
                                            @elseif($dinilai->status_banding == 'proses')
                                                <div class="badge badge-xs light badge-info">Proses</div>
                                            @elseif($dinilai->status_banding == null)
                                                <div class="badge badge-xs   light badge-info">Belum diajukan</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($dinilai->status_penilaian == 'terverifikasi' && $dinilai->status_banding == 'proses')
                                                <a class="btn btn-xs btn-warning mb-1"
                                                    href="{{ route('bp-review-pengajuan', $hash->encode($dinilai->id_penilaian)) }}">Review
                                                    Pengajuan</a>
                                                @if ($dinilai->pengurangan && $dinilai->catatan_penting)
                                                    <a class="btn btn-xs btn-dark mb-1"
                                                        href="{{ route('bp-lihat-catatan', $hash->encode($dinilai->id_penilaian)) }}">Lihat
                                                        Catatan</a>
                                                @endif
                                                <a class="btn btn-xs btn-info mb-1"
                                                    href="{{ route('bp-edit-kpi-performance', $hash->encode($dinilai->id_penilaian)) }}">Edit
                                                    KPI Performance</a>
                                            @else
                                                <div class="badge badge-md light badge-dark">No Action</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('custom-script')

    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>
    <script src={{ asset('assets/vendor/sweetalert2/sweetalert2.all.js') }}></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>

    <!-- All init script -->
    <script src="{{ asset('assets/js/plugins-init/toastr-init.js') }}"></script>
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
    <script>
        $(".swall-yeah").click(function(e) {
            let id = e.target.dataset.id;
            Swal.fire({
                title: 'Apakah anda sudah yakin dengan penilaian ini ?',
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Approve !'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#approve' + id).submit();
                }
            })
        })
    </script>
@endpush
