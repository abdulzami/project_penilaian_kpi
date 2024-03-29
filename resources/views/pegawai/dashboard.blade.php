@extends('layouts.master')

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
@endpush

@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Dashboard</h4>
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
        @penilai
        <div class="col-xl-12 col-xxl-12 col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header pb-3 d-sm-flex d-block">
                    <div>
                        <h4 class="card-title mb-1">Jumlah penilaian {{ strtolower($nama_periode) }}</h4>
                        {{-- <small class="mb-0">Lorem ipsum dolor s</small> --}}
                    </div>
                </div>
                <div class="card-body orders-summary">
                    <div class="row">
                        <div class="col-sm-3 mb-4">
                            <div class="border px-3 py-3 rounded-xl">
                                <h2 class="fs-32 font-w600 counter">{{$belum_dinilai}}</h2>
                                <p class="fs-16 mb-0">Belum Dinilai</p>
                            </div>
                        </div>
                        <div class="col-sm-3 mb-4">
                            <div class="border px-3 py-3 rounded-xl">
                                <h2 class="fs-32 font-w600 counter">{{$menunggu_verifikasi}}</h2>
                                <p class="fs-16 mb-0">Menunggu Verifikasi</p>
                            </div>
                        </div>
                        @atasanpenilai
                        <div class="col-sm-3 mb-4">
                            <div class="border px-3 py-3 rounded-xl">
                                <h2 class="fs-32 font-w600 counter">{{sizeof($membutuhkan_verifikasi)}}</h2>
                                <p class="fs-16 mb-0">Membutuhkan Verifikasi</p>
                            </div>
                        </div>
                        <div class="col-sm-3 mb-4">
                            <div class="border px-3 py-3 rounded-xl">
                                <h2 class="fs-32 font-w600 counter">{{sizeof($membutuhkan_verifikasi2)}}</h2>
                                <p class="fs-16 mb-0">Membutuhkan Verifikasi Dari Banding</p>
                            </div>
                        </div>
                        @endatasanpenilai
                        <div class="col-sm-3 mb-4">
                            <div class="border px-3 py-3 rounded-xl">
                                <h2 class="fs-32 font-w600 counter">{{$banding_penilaian}}</h2>
                                <p class="fs-16 mb-0">Banding Penilaian</p>
                            </div>
                        </div>
                        <div class="col-sm-3 mb-4">
                            <div class="border px-3 py-3 rounded-xl">
                                <h2 class="fs-32 font-w600 counter">{{$selesai}}</h2>
                                <p class="fs-16 mb-0">Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endpenilai
        @dinilai
        @if ($penilaian)
            <div class="col-xl-12">
                <div class="card ">
                    <div class="card-header">
                        <h4 class="card-title mb-1">Penilaian KPI anda</h4>
                    </div>
                    <div class="card-body">
                        @if ($penilaian->status_penilaian != 'terverifikasi' && $penilaian->status_penilaian != 'selesai' && $penilaian->id_banding == null)
                            <div class="col-xl-12">
                                <div class="card text-white bg-info">
                                    <div class="card-body mb-0">
                                        <p class="card-text">Penilaian KPI anda sedang dalam proses.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (($penilaian->status_banding != null) & ($penilaian->status_penilaian != 'selesai'))
                            <div class="col-xl-12">
                                <div class="card text-white bg-info">
                                    <div class="card-body mb-0">
                                        <p class="card-text">Penilaian KPI anda sedang dalam proses banding.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($penilaian->status_penilaian == 'terverifikasi' || $penilaian->status_penilaian == 'selesai' || $penilaian->status_banding != null)
                            <div class="col-xl-12">
                                <div class="card border">
                                    <div class="card-header border-0 pb-0">
                                        <h5 class="card-title">Hasil Penilaian KPI Performance Anda </h5>
                                        @if ($penilaian->status_penilaian != 'selesai' && $penilaian->status_banding == null)
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
                                                        @if ($penilaian->status_banding == 'diterima')
                                                            <th>Realisasi Sebelum Banding</th>
                                                        @endif
                                                        <th>Realisasi</th>
                                                    </tr>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($i = 0; $i < sizeof($kpiperformances); $i++)
                                                        <tr>
                                                            <td>{{ $i + 1 }}</td>
                                                            <td>{{ $kpiperformances[$i]->kategori }}</td>
                                                            <td>{{ $kpiperformances[$i]->tipe_performance }}</td>
                                                            <td>{{ $kpiperformances[$i]->indikator_kpi }}</td>
                                                            <td>{{ $kpiperformances[$i]->definisi }}</td>
                                                            <td>{{ $kpiperformances[$i]->target }} {{ $kpiperformances[$i]->satuan }}</td>
                                                            <td>{{ $kpiperformances[$i]->bobot }}</td>
                                                            @if ($penilaian->status_banding == 'diterima')
                                                                <td>
                                                                    {{ $lama[$i]->realisasi }}
                                                                    {{ $kpiperformances[$i]->satuan }}</td>
                                                            @endif
                                                            <td>{{ $kpiperformances[$i]->realisasi }} {{ $kpiperformances[$i]->satuan }}</td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($penilaian->catatan_penting != null)
                                <div class="col-xl-12">
                                    <div class="card border">
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
                                <div class="card border">
                                    <div class="card-header border-0 pb-0">
                                        <h5 class="card-title">Total Nilai</h5>
                                    </div>
                                    <div class="card-body">
                                        @if ($penilaian->pengurangan == null)
                                            Total nilai anda
                                            <b>{{ $penilaian->total }}</b>
                                        @endif

                                        @if ($penilaian->pengurangan)
                                            Total nilai anda
                                            {{ $penilaian->total }}
                                            dengan pengurangan nilai sebesar {{ $penilaian->pengurangan }} % sehingga menjadi
                                            <b>{{ $penilaian->total - ($penilaian->total * $penilaian->pengurangan) / 100 }}</b>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="card border">
                                    <div class="card-header border-0 pb-0">
                                        <h5 class="card-title">Banding Penilaian</h5>
                                    </div>
                                    <div class="card-body">
                                        Status banding :
                                        @if ($penilaian->status_banding == null)
                                            <span class="badge light badge-dark">Belum diajukan</span>
                                        @elseif($penilaian->status_banding != null && $penilaian->status_penilaian !=
                                            'selesai' )
                                            <span class="badge light badge-info">Sedang di proses</span>
                                        @elseif($penilaian->status_banding == 'ditolak' && $penilaian->status_penilaian ==
                                            'selesai')
                                            <span class="badge light badge-danger">Ditolak</span>
                                        @elseif($penilaian->status_banding == 'diterima' && $penilaian->status_penilaian ==
                                            'selesai')
                                            <span class="badge light badge-primary">Disetujui</span>
                                        @endif
                                        <br><br>
                                        @if ($penilaian->alasan_tolak)
                                            Alasan Tolak : {{ $penilaian->alasan_tolak }}
                                        @endif

                                    </div>

                                    @if ($penilaian->status_banding == null && $penilaian->status_penilaian != 'selesai')
                                        <div class="card-footer border-0 pt-0">
                                            <center><a
                                                    href="{{ route('create-pengajuan-banding', $hash->encode($penilaian->id_penilaian)) }}"><button
                                                        type="submit" class="btn btn-xs btn-primary">Ajukan banding
                                                        nilai</button></a>
                                            </center>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if ($penilaian->status_penilaian == 'selesai' && $penilaian->status_banding == null)
                                <div class="col-xl-12">
                                    <div class="card text-white bg-primary">
                                        <div class="card-body mb-0">
                                            <p class="card-text">Penilaian telah anda setujui. Penilaian selesai.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($penilaian->status_penilaian == 'selesai' && $penilaian->status_banding == 'diterima')
                                <div class="col-xl-12">
                                    <div class="card text-white bg-primary">
                                        <div class="card-body mb-0">
                                            <p class="card-text">Pengajuan banding telah disetujui dan penilaian sudah
                                                diubah.
                                                Penilaian selesai.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($penilaian->status_penilaian == 'selesai' && $penilaian->status_banding == 'ditolak')
                                <div class="col-xl-12">
                                    <div class="card text-white bg-primary">
                                        <div class="card-body mb-0">
                                            <p class="card-text">Pengajuan banding anda ditolak.
                                                Penilaian selesai.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endif
       
        @enddinilai
    </div>
    @endpenilaian_berlangsung

@endsection
@push('custom-script')
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src={{ asset('assets/vendor/sweetalert2/sweetalert2.all.js') }}></script>
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
