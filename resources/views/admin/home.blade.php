@extends('layouts.master')

@push('custom-css')
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
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header border-0 pb-4">
                    <h5 class="card-title">Penilaian <span class="badge badge-info">{{ $nama_periode }}</span>
                        Sedang Berlangsung</h5>
                </div>
            </div>
        </div>
    </div>
    @endpenilaian_berlangsung
@endsection
@push('custom-script')
@endpush
