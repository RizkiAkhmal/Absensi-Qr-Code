@extends('layouts.app')

@section('title', 'Dashboard Pegawai')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Pegawai
            </h1>
            <p class="text-muted">Selamat datang, {{ auth()->user()->name }}!</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-day me-2"></i>Absensi Hari Ini
                    </h5>
                </div>
                <div class="card-body">
                    @if($absensiHariIni)
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    <i class="fas fa-sign-in-alt fa-2x text-success mb-2"></i>
                                    <h6>Jam Masuk</h6>
                                    <h4 class="text-success">{{ $absensiHariIni->jam_masuk ?? '-' }}</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <i class="fas fa-sign-out-alt fa-2x text-danger mb-2"></i>
                                    <h6>Jam Pulang</h6>
                                    <h4 class="text-danger">{{ $absensiHariIni->jam_pulang ?? '-' }}</h4>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <span class="badge bg-{{ $absensiHariIni->status === 'hadir' ? 'success' : ($absensiHariIni->status === 'terlambat' ? 'warning' : 'danger') }} fs-6">
                                {{ ucfirst($absensiHariIni->status) }}
                            </span>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Anda belum melakukan absensi hari ini</p>
                            <a href="{{ route('pegawai.qrcode') }}" class="btn btn-primary">
                                <i class="fas fa-qrcode me-2"></i>Lihat QR Code Saya
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Jadwal Kerja Hari Ini
                    </h5>
                </div>
                <div class="card-body">
                    @if($jadwalHariIni)
                        @if($jadwalHariIni->is_libur)
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-warning mb-3"></i>
                                <h5 class="text-warning">Hari Libur</h5>
                                <p class="text-muted">Anda tidak memiliki jadwal kerja hari ini</p>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-sun fa-2x text-warning mb-2"></i>
                                        <h6>Jam Masuk</h6>
                                        <h4 class="text-primary">{{ $jadwalHariIni->jam_masuk }}</h4>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-moon fa-2x text-secondary mb-2"></i>
                                        <h6>Jam Pulang</h6>
                                        <h4 class="text-primary">{{ $jadwalHariIni->jam_pulang }}</h4>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada jadwal kerja untuk hari ini</p>
                        </div>
                    @endif
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
                            <a href="{{ route('pegawai.qrcode') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-qrcode fa-2x d-block mb-2"></i>
                                QR Code Saya
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('pegawai.absensi') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-history fa-2x d-block mb-2"></i>
                                Riwayat Absensi
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('pegawai.jadwal') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-calendar fa-2x d-block mb-2"></i>
                                Jadwal Kerja
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-outline-warning w-100" onclick="window.print()">
                                <i class="fas fa-print fa-2x d-block mb-2"></i>
                                Cetak QR Code
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($absensiHariIni && !$absensiHariIni->jam_pulang && $jadwalHariIni && !$jadwalHariIni->is_libur)
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Reminder:</strong> Jangan lupa untuk melakukan absen pulang sebelum jam {{ $jadwalHariIni->jam_pulang }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
