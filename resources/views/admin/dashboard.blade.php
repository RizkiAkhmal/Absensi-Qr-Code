@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Total Pegawai</h5>
                            <h2 class="mb-0">{{ $totalPegawai }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-primary bg-opacity-75">
                    <a href="{{ route('admin.pegawai') }}" class="text-white text-decoration-none">
                        <small>Lihat Detail <i class="fas fa-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Absensi Hari Ini</h5>
                            <h2 class="mb-0">{{ $absensiHariIni }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-success bg-opacity-75">
                    <a href="{{ route('admin.laporan') }}" class="text-white text-decoration-none">
                        <small>Lihat Detail <i class="fas fa-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Terlambat Hari Ini</h5>
                            <h2 class="mb-0">{{ $terlambatHariIni }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-warning bg-opacity-75">
                    <a href="{{ route('admin.laporan') }}" class="text-white text-decoration-none">
                        <small>Lihat Detail <i class="fas fa-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Statistik Absensi
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Grafik statistik absensi akan ditampilkan di sini</p>
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar fa-3x text-muted"></i>
                        <p class="mt-2 text-muted">Chart akan diimplementasi dengan Chart.js</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Absensi Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        Data absensi terbaru akan ditampilkan di sini
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tasks me-2"></i>Menu Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.pegawai.create') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-user-plus fa-2x d-block mb-2"></i>
                                Tambah Pegawai
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.jadwal') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-calendar-plus fa-2x d-block mb-2"></i>
                                Atur Jadwal
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.laporan') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-file-alt fa-2x d-block mb-2"></i>
                                Lihat Laporan
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.pegawai') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-users-cog fa-2x d-block mb-2"></i>
                                Kelola Pegawai
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
