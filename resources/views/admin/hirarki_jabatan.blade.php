@extends('layouts.master')

@push('custom-css')
@endpush

@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Hirarki {{$jabatan->nama_jabatan}} {{$jabatan->nama_struktural}} {{$jabatan->nama_bidang}}</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('jabatan') }}">Jabatan</a></li>
                <li class="breadcrumb-item active">Hirarki Jabatan</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-6 col-xxl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="mr-3 bgl-primary text-primary">
                            <i class="flaticon-381-user-3"></i>
                        </span>
                        <div class="media-body">
                            <h4 class="mb-0 text-black"><span class="counter ml-0">
                                @if ($penilai)
                                {{$penilai->nama_jabatan}} {{$penilai->nama_struktural}} {{$penilai->nama_bidang}}
                                @else
                                    Tidak ada
                                @endif
                                
                            </span></h4>
                            <p class="mb-0">Penilai</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-xxl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="widget-stat card">
                <div class="card-body p-4">
                    <div class="media ai-icon">
                        <span class="mr-3 bgl-primary text-primary">
                             <i class="flaticon-381-user-1"></i>
                            
                        </span>
                        <div class="media-body">
                            <h4 class="mb-0 text-black"><span class="counter ml-0">
                                @if ($atasan_penilai)
                                {{$atasan_penilai->nama_jabatan}} {{$atasan_penilai->nama_struktural}} {{$atasan_penilai->nama_bidang}}
                                @else
                                    Tidak ada
                                @endif    
                            </span></h4>
                            <p class="mb-0">Atasan Penilai</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('custom-script')
@endpush
