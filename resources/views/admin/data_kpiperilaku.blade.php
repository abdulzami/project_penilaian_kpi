@extends('layouts.master')

@push('custom-css')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
@endpush

@section('content-header')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>KPI Perilaku</h4>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Data KPI Perilaku</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table small">
                            <thead>
                                <tr>
                                    <th style="width: 100px">Nama KPI</th>
                                    <th>Ekselen (5)</th>
                                    <th>Baik (4)</th>
                                    <th>Cukup (3)</th>
                                    <th>Kurang (2)</th>
                                    <th>Kurang Sekali (1)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($perilakus as $perilaku)
                                    <tr>
                                        <td><strong>{{$perilaku->nama_kpi}}</strong></td>
                                        <td>{{$perilaku->ekselen}}</td>
                                        <td>{{$perilaku->baik}}</td>
                                        <td>{{$perilaku->cukup}}</td>
                                        <td>{{$perilaku->kurang}}</td>
                                        <td>{{$perilaku->kurang_sekali}}</td>
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
@endpush
