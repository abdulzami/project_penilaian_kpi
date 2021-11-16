@extends('layouts.master')

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
@endpush

@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Home</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            Selamat datang, {{ Auth::user()->nama }}
        </div>
    </div>
@endsection
@section('content')
    @penilaian_berlangsung
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pb-4">
                    <h5 class="card-title">Penilaian {{ strtolower($nama_periode) }}
                        Sedang Berlangsung</h5>
                </div>
            </div>
        </div>
        @dinilai
        @if ($penilaian->status_penilaian != 'terverifikasi' && $penilaian->status_penilaian != 'selesai')
            <div class="col-xl-12">
                <div class="card text-white bg-info">
                    <div class="card-body mb-0">
                        <p class="card-text">Penilaian KPI anda sedang dalam proses.</p>
                    </div>
                </div>
            </div>
        @endif
        @if ($penilaian->status_penilaian == 'terverifikasi' || $penilaian->status_penilaian == 'selesai')
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h5 class="card-title">Hasil Penilaian KPI Performance Anda </h5>
                        @if ($penilaian->status_penilaian != 'selesai')
                            <a href="#" class="btn btn-xs btn-primary mb-1 swall-yeah"
                                data-id="{{ $hash->encode($penilaian->id_penilaian) }}">
                                <form
                                    action="{{ route('approve-penilaian-dinilai', $hash->encode($penilaian->id_penilaian)) }}"
                                    id="approve{{ $hash->encode($penilaian->id_penilaian) }}" method="post">
                                    @method('put')
                                    @csrf
                                </form>
                                Approve
                            </a>
                        @endif

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                    <tr>
                                        <th>No</th>
                                        <th>Kategori</th>
                                        <th>Tipe Performance</th>
                                        <th>Indikator KPI</th>
                                        <th>Definisi</th>
                                        <th>Target</th>
                                        <th>Bobot</th>
                                        <th>Realisasi</th>
                                    </tr>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kpiperformances as $index => $kpip)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $kpip->kategori }}</td>
                                            <td>{{ $kpip->tipe_performance }}</td>
                                            <td>{{ $kpip->indikator_kpi }}</td>
                                            <td>{{ $kpip->definisi }}</td>
                                            <td>{{ $kpip->target }} {{ $kpip->satuan }}</td>
                                            <td>{{ $kpip->bobot }}</td>
                                            <td>{{ $kpip->realisasi }} {{ $kpip->satuan }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @if ($penilaian->catatan_penting != null)
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h5 class="card-title text-danger">Catatan Penting !!</h5>
                        </div>
                        <div class="card-body">
                            {{ $penilaian->catatan_penting }}
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h5 class="card-title">Total Nilai</h5>
                    </div>
                    @if ($penilaian->pengurangan != null)
                        <div class="card-body">Total nilai anda adalah
                            {{ $penilaian->total }}
                            dengan pengurangan nilai sebesar {{ $penilaian->pengurangan }} % sehingga menjadi
                            <b>{{ $penilaian->total - ($penilaian->total * $penilaian->pengurangan) / 100 }}</b>
                        </div>
                    @else
                        <div class="card-body">Total nilai anda adalah
                            <b>{{ $penilaian->total }}</b>
                        </div>
                    @endif

                </div>
            </div>
            @if ($penilaian->status_penilaian != 'selesai')
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h5 class="card-title">Banding Penilaian</h5>
                        </div>
                        <div class="card-body">
                            Status banding : 
                                @if ($penilaian->status_banding == null)
                                <span class="badge badge-light">Belum diajukan</span>
                                @elseif($penilaian->status_banding == 'proses')
                                <span class="badge badge-light">Info</span>
                                @elseif($penilaian->status_banding == 'ditolak')
                                <span class="badge badge-danger">Ditolak</span>
                                @elseif($penilaian->status_banding == 'diterima')
                                <span class="badge badge-primary">Diterima</span>
                                @endif
                        </div>
                        <div class="card-footer border-0 pt-0">
                            <center><button type="submit" class="btn btn-xs btn-primary mt-3">Ajukan banding nilai</button>
                            </center>
                        </div>
                    </div>
                </div>
            @endif
            @if ($penilaian->status_penilaian == 'selesai')
                <div class="col-xl-12">
                    <div class="card text-white bg-primary">
                        <div class="card-body mb-0">
                            <p class="card-text">Penilaian telah anda setujui.</p>
                        </div>
                    </div>
                </div>
            @endif
        @endif
        @enddinilai
    </div>
    @endpenilaian_berlangsung
    @penilai

    @endpenilai
@endsection
@push('custom-script')
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src={{ asset('assets/vendor/sweetalert2/sweetalert2.all.js') }}></script>
    <!-- All init script -->
    <script src="{{ asset('assets/js/plugins-init/toastr-init.js') }}"></script>
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
