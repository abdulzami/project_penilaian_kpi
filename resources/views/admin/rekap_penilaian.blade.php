@extends('layouts.master')

@push('custom-css')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
@endpush

@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Rekap Penilaian</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Rekap Penilaian</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Filter Penilaian</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form action="{{ route('rekap-penilaian-filtered') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label>Jadwal (Periode)</label>
                                <select id="single-select" name="jadwal">
                                    <option value="">Pilih</option>
                                    @foreach ($jadwals as $jadwal)
                                        <option value="{{ $hash->encode($jadwal->id_jadwal) }}">{{ $jadwal->nama_periode }} | {{date('d-M-Y', strtotime($jadwal->tanggal_mulai))}} - {{date('d-M-Y', strtotime($jadwal->tanggal_akhir))}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Struktural</label>
                                <select id="single-select2" name="struktural">
                                    <option value="">Pilih</option>
                                    @foreach ($bidangs as $bidang)
                                        <option value="{{ $hash->encode($bidang->id_bidang) }}">
                                            {{ $bidang->nama_struktural }} {{ $bidang->nama_bidang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary mt-3">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Data Penilaian

                        @if ($filter == "filter")
                            <div class="badge badge-info">Filtered</div>
                        @endif
                    </h4>
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
                                    {{-- <th>Total Setelah Banding</th> --}}
                                    <th>Total</th>
                                    <th style="width: 100px">Capaian</th>
                                    <th>Nama Periode</th>
                                    <th>Status</th>
                                </tr>
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
                                            @elseif($dinilai->total_sebelum_banding == 'tidak_banding')
                                                <td>
                                                    <div class="badge badge-xs light badge-info">Tidak melakukan banding</div>
                                                </td>
                                            @else
                                                <td>{{ $dinilai->total_sebelum_banding }}</td>
                                            @endif
    
                                            <td>{{ $dinilai->total }}</td>
                                            <td>{{ $dinilai->capaian }}</td>
                                            <td>{{$dinilai->nama_periode}}</td>
                                            <td>
                                                @if ($dinilai->status == 'selesai')
                                                    <div class="badge badge-xs light badge-primary">Sudah Selesai</div>
                                                @else
                                                <div class="badge badge-xs light badge-warning">Belum Selesai</div>
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
    <script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/select2-init.js') }}"></script>

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
