@extends('layouts.app')

@section('title', 'Kelola Jadwal Kerja')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-calendar me-2"></i>Kelola Jadwal Kerja
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>Jadwal Kerja Pegawai
                    </h5>
                    <div>
                        <a href="{{ route('admin.jadwal.bulk.create') }}" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-users-cog me-2"></i>Atur Jadwal Massal
                        </a>
                        <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#copyScheduleModal">
                            <i class="fas fa-copy me-2"></i>Copy Jadwal
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($pegawai->count() > 0)
                        <div class="row">
                            @foreach($pegawai as $p)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-user me-2"></i>{{ $p->name }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($p->jadwalKerja->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Hari</th>
                                                            <th>Jam</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($p->jadwalKerja as $jadwal)
                                                        <tr>
                                                            <td>{{ $jadwal->hari }}</td>
                                                            <td>
                                                                @if($jadwal->is_libur)
                                                                    <span class="text-warning">LIBUR</span>
                                                                @else
                                                                    <small>{{ $jadwal->jam_masuk }} - {{ $jadwal->jam_pulang }}</small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($jadwal->is_libur)
                                                                    <i class="fas fa-calendar-times text-warning"></i>
                                                                @else
                                                                    <i class="fas fa-check text-success"></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-3">
                                                <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                                                <p class="text-muted mb-0">Belum ada jadwal</p>
                                            </div>
                                        @endif
                                        
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('admin.jadwal.create', $p->id) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-{{ $p->jadwalKerja->count() > 0 ? 'edit' : 'plus' }} me-1"></i>
                                                {{ $p->jadwalKerja->count() > 0 ? 'Edit Jadwal' : 'Buat Jadwal' }}
                                            </a>
                                            @if($p->jadwalKerja->count() > 0)
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    {{ $p->jadwalKerja->count() }}/7 hari diatur
                                                </small>
                                            @else
                                                <small class="text-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Belum ada jadwal
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Pegawai</h5>
                            <p class="text-muted">Tambahkan pegawai terlebih dahulu untuk mengatur jadwal kerja.</p>
                            <a href="{{ route('admin.pegawai.create') }}" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>Tambah Pegawai
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Copy Schedule Modal -->
<div class="modal fade" id="copyScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-copy me-2"></i>Copy Jadwal Kerja
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.jadwal.copy') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Copy Dari Pegawai:</label>
                        <select class="form-select" name="from_user_id" required>
                            <option value="">Pilih pegawai sumber...</option>
                            @foreach($pegawai as $p)
                                @if($p->jadwalKerja->count() > 0)
                                    <option value="{{ $p->id }}">
                                        {{ $p->name }} ({{ $p->jadwalKerja->count() }}/7 hari)
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted">Hanya pegawai yang sudah memiliki jadwal yang ditampilkan</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Copy Ke Pegawai:</label>
                        <div class="row">
                            @foreach($pegawai as $p)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="to_user_ids[]" value="{{ $p->id }}" id="copy_to_{{ $p->id }}">
                                        <label class="form-check-label" for="copy_to_{{ $p->id }}">
                                            {{ $p->name }}
                                            @if($p->jadwalKerja->count() > 0)
                                                <small class="text-warning">(akan ditimpa)</small>
                                            @else
                                                <small class="text-muted">(belum ada jadwal)</small>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Jadwal yang sudah ada akan ditimpa dengan jadwal baru.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-copy me-2"></i>Copy Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
