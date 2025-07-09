@extends('layouts.app')

@section('title', 'Jadwal Kerja')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-calendar me-2"></i>Jadwal Kerja Saya
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Jadwal Kerja Mingguan
                    </h5>
                </div>
                <div class="card-body">
                    @if($jadwal->count() > 0)
                        <div class="row">
                            @php
                                $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                $hariIni = \Carbon\Carbon::now()->locale('id')->dayName;
                            @endphp
                            
                            @foreach($hari as $h)
                                @php
                                    $jadwalHari = $jadwal->where('hari', $h)->first();
                                @endphp
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card {{ $h === $hariIni ? 'border-primary' : '' }}">
                                        <div class="card-header {{ $h === $hariIni ? 'bg-primary text-white' : 'bg-light' }}">
                                            <h6 class="card-title mb-0">
                                                @if($h === $hariIni)
                                                    <i class="fas fa-star me-1"></i>
                                                @endif
                                                {{ $h }}
                                                @if($h === $hariIni)
                                                    <small class="ms-2">(Hari Ini)</small>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if($jadwalHari)
                                                @if($jadwalHari->is_libur)
                                                    <div class="text-center">
                                                        <i class="fas fa-calendar-times fa-2x text-warning mb-2"></i>
                                                        <p class="text-warning mb-0">
                                                            <strong>LIBUR</strong>
                                                        </p>
                                                    </div>
                                                @else
                                                    <div class="text-center">
                                                        <div class="mb-2">
                                                            <i class="fas fa-sun text-warning me-1"></i>
                                                            <strong>Masuk:</strong>
                                                            <span class="text-success">{{ $jadwalHari->jam_masuk }}</span>
                                                        </div>
                                                        <div>
                                                            <i class="fas fa-moon text-secondary me-1"></i>
                                                            <strong>Pulang:</strong>
                                                            <span class="text-danger">{{ $jadwalHari->jam_pulang }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-center">
                                                    <i class="fas fa-question-circle fa-2x text-muted mb-2"></i>
                                                    <p class="text-muted mb-0">
                                                        <small>Jadwal belum diatur</small>
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Jadwal Kerja Belum Diatur</h5>
                            <p class="text-muted">Hubungi admin untuk mengatur jadwal kerja Anda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($jadwal->count() > 0)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Ringkasan Jadwal
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-success">{{ $jadwal->where('is_libur', false)->count() }}</h4>
                                <small class="text-muted">Hari Kerja</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ $jadwal->where('is_libur', true)->count() }}</h4>
                            <small class="text-muted">Hari Libur</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informasi
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Absensi dilakukan dengan scan QR Code
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock text-warning me-2"></i>
                            Terlambat jika absen setelah jam masuk
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-calendar text-info me-2"></i>
                            Jadwal dapat berubah sewaktu-waktu
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
