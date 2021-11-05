@extends('layouts.master')

@push('custom-css')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
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
                <li class="breadcrumb-item active">Manajemen KPI Perfomance</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Data KPI Performance</h4>
                    <a href="{{ route('create-kpiperformance', $id) }}"><button type="button"
                            class="btn btn-xs mb-3 btn-primary mb-1">Tambah
                            Data</button></a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kategori</th>
                                    <th>Indikator KPI</th>
                                    <th>Definisi</th>
                                    <th>Satuan</th>
                                    <th>Target</th>
                                    <th>Bobot</th>
                                    <th>Action</th>
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
                                        <td>{{ $kpip->indikator_kpi }}</td>
                                        <td>{{ $kpip->definisi }}</td>
                                        <td>{{ $kpip->satuan }}</td>
                                        <td>{{ $kpip->target }}</td>
                                        <td>{{ $kpip->bobot }}</td>
                                        <td>
                                            <a href="{{ route('edit-kpiperformance', [$hash->encode($kpip->id_jabatan), $hash->encode($kpip->id_performance)]) }}" class="btn btn-xs btn-info mb-1">Edit</a>
                                            <a href="#" class="btn btn-xs btn-danger mb-1 swall-yeah"
                                                data-id="{{ $hash->encode($kpip->id_performance) }}">
                                                <form
                                                    action="{{ route('delete-kpiperformance', [$hash->encode($kpip->id_jabatan), $hash->encode($kpip->id_performance)]) }}"
                                                    id="delete{{ $hash->encode($kpip->id_performance) }}" method="post">
                                                    @method('delete')
                                                    @csrf
                                                </form>
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <th colspan="6"></th>
                                    <th>Total Bobot :
                                        @if ($total_bobot != 100)
                                            <span class="badge badge-warning">{{ $total_bobot }}</span>
                                        @else
                                            <span class="badge badge-primary">{{ $total_bobot }}</span>
                                        @endif
                                    </th>
                                </tr>
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
