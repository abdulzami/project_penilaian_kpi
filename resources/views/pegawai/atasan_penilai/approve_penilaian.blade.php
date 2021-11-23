@extends('layouts.master')

@push('custom-css')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
@endpush

@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Approve Penilaian</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Penilaian Yang Membutuhkan Verifikasi</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Membutuhkan Verifikasi</h4>
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
                                    <th>Total</th>
                                    {{-- <th>Kinerja</th> --}}
                                    <th style="width: 100px">Capaian</th>
                                    <th>Keterangan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penilaians as $index => $dinilai)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $dinilai->npk }}</td>
                                        <td>{{ $dinilai->nama }}</td>
                                        <td>{{ $dinilai->nama_jabatan }} {{ $dinilai->nama_struktural }}
                                            {{ $dinilai->nama_bidang }}</td>
                                        <td>{{ $dinilai->total }}</td>
                                        {{-- <td></td> --}}
                                        <td>{{ $dinilai->capaian }}</td>
                                        <td>
                                            @if ($dinilai->performance != 0)
                                                <div class="badge badge-xs light badge-primary">KPI Performance <i
                                                        class="fa fa-check"></i></div><br>
                                            @else
                                                <div class="badge badge-xs light badge-danger">KPI Performance <i
                                                        class="fa fa-close"></i></div><br>
                                            @endif
                                            @if ($dinilai->perilaku != 0)
                                                <div class="badge badge-xs light badge-primary">KPI Perilaku <i
                                                        class="fa fa-check"></i></div><br>
                                            @else
                                                <div class="badge badge-xs light badge-danger">KPI Perilaku <i
                                                        class="fa fa-close"></i></div><br>
                                            @endif
                                            @if ($dinilai->catatan_penting != '')
                                                <div class="badge badge-xs light badge-primary">Catatan Penting <i
                                                        class="fa fa-check"></i></div>
                                            @else
                                                <div class="badge badge-xs light badge-warning">Catatan Penting <i
                                                        class="fa fa-close"></i></div><br>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($dinilai->catatan_penting == null)
                                                <a href="#" class="btn btn-xs btn-secondary mb-1 swall-yeah"
                                                    data-id="{{ $hash->encode($dinilai->id_penilaian) }}">
                                                    <form
                                                        action="{{ route('approve-penilaian-approve-langsung', $hash->encode($dinilai->id_penilaian)) }}"
                                                        id="approve{{ $hash->encode($dinilai->id_penilaian) }}"
                                                        method="post">
                                                        @csrf
                                                        @method('put')
                                                    </form>
                                                    Approve
                                                </a>
                                            @else
                                                <a class="btn btn-xs btn-info mb-1"
                                                    href="{{ route('approve-penilaian-review', $hash->encode($dinilai->id_penilaian)) }}">Review</a>
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
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Membutuhkan Verifikasi dari Banding Penilaian</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example5" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NPK</th>
                                    <th>Nama Pegawai</th>
                                    <th>Jabatan</th>
                                    <th>Total Sebelum Pengurangan</th>
                                    <th>Total Sebelum Banding</th>
                                    <th>Total</th>
                                    {{-- <th>Kinerja</th> --}}
                                    <th style="width: 100px">Capaian</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penilaians2 as $index => $dinilai)
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
                                        <td>{{ $dinilai->total }}</td>
                                        {{-- <td></td> --}}
                                        <td>{{ $dinilai->capaian }}</td>
                                        <td>
                                            <a href="#" class="btn btn-xs btn-secondary mb-1 swall-yeah"
                                                data-id="{{ $hash->encode($dinilai->id_penilaian) }}">
                                                <form
                                                    action="{{ route('approve-banding-penilaian', $hash->encode($dinilai->id_penilaian)) }}"
                                                    id="approve{{ $hash->encode($dinilai->id_penilaian) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('put')
                                                </form>
                                                Approve
                                            </a>
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
