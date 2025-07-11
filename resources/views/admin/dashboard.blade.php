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
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number">{{ $totalPegawai }}</div>
                    <div class="stats-label">Total Pegawai</div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center">
                    <a href="{{ route('admin.pegawai') }}" class="text-white text-decoration-none">
                        <small><i class="fas fa-arrow-right me-1"></i>Lihat Detail</small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card stats-card success">
                <div class="card-body text-center">
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number">{{ $absensiHariIni }}</div>
                    <div class="stats-label">Hadir Hari Ini</div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center">
                    <a href="{{ route('admin.laporan') }}" class="text-white text-decoration-none">
                        <small><i class="fas fa-arrow-right me-1"></i>Lihat Detail</small>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card stats-card warning">
                <div class="card-body text-center">
                    <div class="stats-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stats-number">{{ $terlambatHariIni }}</div>
                    <div class="stats-label">Terlambat Hari Ini</div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center">
                    <a href="{{ route('admin.laporan') }}" class="text-white text-decoration-none">
                        <small><i class="fas fa-arrow-right me-1"></i>Lihat Detail</small>
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
                    <canvas id="attendanceChart" width="400" height="200"></canvas>
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
                                @forelse($absensiTerbaru as $absensi)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <strong>{{ $absensi->user->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $absensi->tanggal }}</small><br>
                                        <strong>{{ $absensi->jam_masuk ?? $absensi->jam_pulang }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $absensi->status === 'hadir' ? 'success' : ($absensi->status === 'terlambat' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($absensi->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        Belum ada data absensi
                                    </td>
                                </tr>
                                @endforelse
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
                            <a href="{{ route('admin.pegawai.create') }}" class="quick-action-card">
                                <div class="quick-action-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="quick-action-title">Tambah Pegawai</div>
                                <div class="quick-action-desc">Daftarkan pegawai baru ke sistem</div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.jadwal') }}" class="quick-action-card">
                                <div class="quick-action-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div class="quick-action-title">Atur Jadwal</div>
                                <div class="quick-action-desc">Kelola jadwal kerja pegawai</div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.laporan') }}" class="quick-action-card">
                                <div class="quick-action-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="quick-action-title">Lihat Laporan</div>
                                <div class="quick-action-desc">Analisis data absensi</div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.pegawai') }}" class="quick-action-card">
                                <div class="quick-action-icon">
                                    <i class="fas fa-users-cog"></i>
                                </div>
                                <div class="quick-action-title">Kelola Pegawai</div>
                                <div class="quick-action-desc">Edit data dan QR code pegawai</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}

.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 20px;
    color: white;
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
}

.stats-card.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stats-card.success:hover {
    box-shadow: 0 20px 40px rgba(17, 153, 142, 0.3);
}

.stats-card.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stats-card.warning:hover {
    box-shadow: 0 20px 40px rgba(240, 147, 251, 0.3);
}

.stats-icon {
    font-size: 3rem;
    opacity: 0.8;
    margin-bottom: 1rem;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stats-label {
    font-size: 1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.chart-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.recent-activity {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    padding: 1rem;
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.3s ease;
}

.activity-item:hover {
    background: #f8f9fa;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
}

.quick-action-card {
    text-align: center;
    padding: 2rem 1rem;
    border-radius: 16px;
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    border: 2px solid #e9ecef;
    background: white;
}

.quick-action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    text-decoration: none;
    color: inherit;
}

.quick-action-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.quick-action-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.quick-action-desc {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Attendance Chart
const ctx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            @foreach($statistikAbsensi as $stat)
                '{{ $stat["hari"] }}',
            @endforeach
        ],
        datasets: [{
            label: 'Hadir',
            data: [
                @foreach($statistikAbsensi as $stat)
                    {{ $stat["hadir"] }},
                @endforeach
            ],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'Terlambat',
            data: [
                @foreach($statistikAbsensi as $stat)
                    {{ $stat["terlambat"] }},
                @endforeach
            ],
            borderColor: 'rgb(255, 205, 86)',
            backgroundColor: 'rgba(255, 205, 86, 0.2)',
            tension: 0.1
        }, {
            label: 'Alpha',
            data: [
                @foreach($statistikAbsensi as $stat)
                    {{ $stat["alpha"] }},
                @endforeach
            ],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Statistik Absensi 7 Hari Terakhir'
            }
        }
    }
});
</script>
@endpush
