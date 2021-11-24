@extends('layouts.master')

@push('custom-css')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
@endpush

@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Jabatan</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('jabatan') }}">Jabatan</a></li>
                <li class="breadcrumb-item active">Manajemen Jabatan</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Data Jabatan</h4>
                    <a href="{{ route('create-jabatan') }}"><button type="button" class="btn btn-xs mb-3 btn-primary mb-1">Tambah
                            Data</button></a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example4" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Jabatan</th>
                                    <th>Keterangan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jabatans as $index => $jabatan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $jabatan->nama_jabatan }} {{ $jabatan->nama_struktural }}
                                            {{ $jabatan->nama_bidang }}</td>
                                        <td>
                                            @if ($jabatan->id_penilai)
                                                @if ($jabatan->total_bobot_jabatan == 100)
                                                    <span class="badge light badge-success">Total bobot kpi performance :
                                                        100. Sudah siap
                                                        dilakukan penilaian</span>
                                                @else
                                                    <span class="badge light badge-warning">Total bobot kpi performance :
                                                        {{ $jabatan->total_bobot_jabatan == null ? 0 : $jabatan->total_bobot_jabatan }}.
                                                        Belum siap dilakukan
                                                        penilaian</span>
                                                @endif
                                            @else
                                                <span class="badge light badge-dark">Tidak mempunyai penilai</span>
                                            @endif

                                        </td>
                                        {{-- @if ($jabatan->nama_penilai == null)
                                            <td> <span class="badge light-xs badge-danger">Tidak ada penilainya</span></td>
                                        @else
                                            <td>{{ $jabatan->nama_penilai }} {{ $jabatan->nama_struktural_penilai }} {{ $jabatan->nama_bidang_penilai }}</td>
                                        @endif --}}
                                        <td>
                                            @if ($jabatan->id_penilai)
                                                <a class="btn btn-xs btn-success mb-1"
                                                    href="{{ route('kpiperformance', $hash->encode($jabatan->id_jabatan)) }}">KPI
                                                    Performance</a>
                                            @endif
                                            <a class="btn btn-xs btn-primary mb-1"
                                                href="{{ route('hirarki-jabatan', $hash->encode($jabatan->id_jabatan)) }}">Hirarki</a>
                                            <a class="btn btn-xs btn-info px-2 mb-1"
                                                href="{{ route('edit-jabatan', $hash->encode($jabatan->id_jabatan)) }}"><i class="fa fa-pencil"></i></a>
                                            <a href="#" class="btn btn-xs btn-danger px-2 mb-1 swall-yeah"
                                                data-id="{{ $hash->encode($jabatan->id_jabatan) }}">
                                                <form
                                                    action="{{ route('delete-jabatan', $hash->encode($jabatan->id_jabatan)) }}"
                                                    id="delete{{ $hash->encode($jabatan->id_jabatan) }}" method="post">
                                                    @method('delete')
                                                    @csrf
                                                </form>
                                                <i class="fa fa-trash"></i>
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
    <script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.all.js') }}"></script>
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
        $(".swall-yeah").click(function() {
            let id = $(this).data('id') 
            Swal.fire({
                title: 'Apakah anda yakin ingin menghapus data ini ?',
                text: "Anda tidak akan bisa mengembalikan nya!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus !'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#delete' + id).submit();
                }
            })
        })
    </script>
@endpush
