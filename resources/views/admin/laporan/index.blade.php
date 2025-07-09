@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-chart-bar me-2"></i>Laporan Absensi
            </h1>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>Filter Laporan
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.laporan.filter') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="tanggal_mulai" 
                                       name="tanggal_mulai" 
                                       value="{{ request('tanggal_mulai') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="tanggal_selesai" 
                                       name="tanggal_selesai" 
                                       value="{{ request('tanggal_selesai') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="user_id" class="form-label">Pegawai</label>
                                <select class="form-select" id="user_id" name="user_id">
                                    <option value="">Semua Pegawai</option>
                                    @if(isset($pegawai))
                                        @foreach($pegawai as $p)
                                            <option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>Data Absensi
                    </h6>
                    <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i>Export Excel
                    </button>
                </div>
                <div class="card-body">
                    @if($absensi->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped" id="absensiTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pegawai</th>
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
                                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                                <strong>{{ $a->user->name }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($a->tanggal)->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($a->tanggal)->locale('id')->dayName }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($a->jam_masuk)
                                                <span class="badge bg-success">
                                                    {{ $a->jam_masuk }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($a->jam_pulang)
                                                <span class="badge bg-danger">
                                                    {{ $a->jam_pulang }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $a->status === 'hadir' ? 'success' : ($a->status === 'terlambat' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($a->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($a->jam_masuk && $a->jam_pulang)
                                                @php
                                                    $masuk = \Carbon\Carbon::parse($a->jam_masuk);
                                                    $pulang = \Carbon\Carbon::parse($a->jam_pulang);
                                                    $durasi = $masuk->diff($pulang);
                                                @endphp
                                                {{ $durasi->format('%H:%I') }}
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
                            {{ $absensi->appends(request()->query())->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak Ada Data</h5>
                            <p class="text-muted">Tidak ada data absensi untuk filter yang dipilih.</p>
                        </div>
                    @endif
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
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportToExcel() {
    const table = document.getElementById('absensiTable');
    const wb = XLSX.utils.table_to_book(table, {sheet: "Laporan Absensi"});
    XLSX.writeFile(wb, 'laporan_absensi.xlsx');
}
</script>
@endpush
