@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-history me-2"></i>Riwayat Absensi
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Data Absensi Anda
                    </h5>
                </div>
                <div class="card-body">
                    @if($absensi->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Pulang</th>
                                        <th>Status</th>
                                        <th>Durasi Kerja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($absensi as $index => $a)
                                    <tr>
                                        <td>{{ $absensi->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar-day text-primary me-2"></i>
                                                {{ \Carbon\Carbon::parse($a->tanggal)->format('d/m/Y') }}
                                                <small class="text-muted ms-2">
                                                    ({{ \Carbon\Carbon::parse($a->tanggal)->locale('id')->dayName }})
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($a->jam_masuk)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-sign-in-alt me-1"></i>
                                                    {{ $a->jam_masuk }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($a->jam_pulang)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-sign-out-alt me-1"></i>
                                                    {{ $a->jam_pulang }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($a->status)
                                                <span class="badge bg-{{ $a->status === 'hadir' ? 'success' : ($a->status === 'terlambat' ? 'warning' : 'danger') }}">
                                                    @if($a->status === 'hadir')
                                                        <i class="fas fa-check me-1"></i>
                                                    @elseif($a->status === 'terlambat')
                                                        <i class="fas fa-clock me-1"></i>
                                                    @else
                                                        <i class="fas fa-times me-1"></i>
                                                    @endif
                                                    {{ ucfirst($a->status) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($a->jam_masuk && $a->jam_pulang)
                                                @php
                                                    $masuk = \Carbon\Carbon::parse($a->jam_masuk);
                                                    $pulang = \Carbon\Carbon::parse($a->jam_pulang);
                                                    $durasi = $masuk->diff($pulang);
                                                @endphp
                                                <span class="text-info">
                                                    <i class="fas fa-hourglass-half me-1"></i>
                                                    {{ $durasi->format('%H:%I') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($absensi->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $absensi->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Data Absensi</h5>
                            <p class="text-muted">Anda belum melakukan absensi. Mulai absensi dengan scan QR Code.</p>
                            <a href="{{ route('pegawai.qrcode') }}" class="btn btn-primary">
                                <i class="fas fa-qrcode me-2"></i>Scan QR Code Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($absensi->count() > 0)
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h5>Total Hadir</h5>
                    <h3>{{ $absensi->where('status', 'hadir')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h5>Total Terlambat</h5>
                    <h3>{{ $absensi->where('status', 'terlambat')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                    <h5>Total Alpha</h5>
                    <h3>{{ $absensi->where('status', 'alpha')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
