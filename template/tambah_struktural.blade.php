@extends('layouts.master')

@push('custom-css')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endpush
@section('strstruktural')

@endsection
@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Tambah Struktural</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('struktural') }}">Struktural</a></li>
                <li class="breadcrumb-item active">Tambah Struktural</li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Form Tambah Struktural</h4>
        </div>
        <div class="card-body">
            <div class="basic-form">
                <form>
                    <div class="form-group">
                        <label>Nama Struktural</label>
                        <input type="text" class="form-control" placeholder="Masukkan nama struktural">
                    </div>
                    <button type="submit" class="btn btn-md btn-success mt-3">Simpan</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('custom-script')
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>
@endpush
