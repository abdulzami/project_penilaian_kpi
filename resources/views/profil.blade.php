@extends('layouts.master')

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
@endpush

@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Profil</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Profil anda</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="profile card card-body px-3 pt-3 pb-0">
                <div class="profile-head">
                    <div class="profile-info">
                        <div class="profile-details">
                            <div class="profile-name px-3 pt-2">
                                <h4 class="text-primary mb-0">{{ Auth::user()->nama }}</h4>
                                @if (Auth::user()->level == 'pegawai')
                                    <p>{{ $jabatan }}</p>
                                @endif
                            </div>
                            <div class="profile-email px-2 pt-2">
                                <h4 class="text-muted mb-0">{{ Auth::user()->email }}</h4>
                                <p>Email</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-1">Role</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if (Auth::user()->level == 'admin')
                            <div class="col-xl-4">
                                <div class="widget-stat card border">
                                    <div class="card-body">
                                        <div class="media ai-icon">
                                            <span class="mr-3 bgl-primary text-primary">
                                                <i class="flaticon-381-user-3"></i>
                                            </span>
                                            <div class="media-body">
                                                <h5 class="mb-0 text-black"><span class="counter ml-0">
                                                        Admin
                                                    </span>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (Auth::user()->level == 'pegawai')
                            @atasanpenilai
                            <div class="col-xl-4">
                                <div class="widget-stat card border">
                                    <div class="card-body">
                                        <div class="media ai-icon">
                                            <span class="mr-3 bgl-primary text-primary">
                                                <i class="flaticon-381-user-3"></i>
                                            </span>
                                            <div class="media-body">
                                                <h5 class="mb-0 text-black"><span class="counter ml-0">
                                                        Approver (Atasan Tidak Langsung)
                                                    </span>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endatasanpenilai
                            @penilai
                            <div class="col-xl-4">
                                <div class="widget-stat card border">
                                    <div class="card-body">
                                        <div class="media ai-icon">
                                            <span class="mr-3 bgl-primary text-primary">
                                                <i class="flaticon-381-user-3"></i>
                                            </span>
                                            <div class="media-body">
                                                <h5 class="mb-0 text-black"><span class="counter ml-0">
                                                        Penilai (Atasan Langsung)
                                                    </span>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endpenilai
                            @dinilai
                            <div class="col-xl-4">
                                <div class="widget-stat card border">
                                    <div class="card-body">
                                        <div class="media ai-icon">
                                            <span class="mr-3 bgl-primary text-primary">
                                                <i class="flaticon-381-user-3"></i>
                                            </span>
                                            <div class="media-body">
                                                <h5 class="mb-0 text-black"><span class="counter ml-0">
                                                        Dinilai
                                                    </span>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @enddinilai
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-1">Ganti password</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form action="{{ route('ganti-password') }}" method="post">
                            @csrf
                            @method('put')
                            <div class="form-group">
                                <label>Password lama</label>
                                <input type="password" name="password_lama" class="form-control"
                                    placeholder="Masukkan password lama">
                            </div>
                            <div class="form-group">
                                <label>Password Baru</label>
                                <input type="password" name="password_baru" class="form-control"
                                    placeholder="Masukkan password baru">
                            </div>
                            <div class="form-group">
                                <label>Ulangi Password Baru</label>
                                <input type="password" name="ulangi_password_baru" class="form-control"
                                    placeholder="Masukkan password baru">
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary mt-3">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
