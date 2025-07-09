@extends('layouts.app')

@section('title', 'Kelola Pegawai')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">
                    <i class="fas fa-users me-2"></i>Kelola Pegawai
                </h1>
                <a href="{{ route('admin.pegawai.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Pegawai
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>QR Code</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pegawai as $index => $p)
                                <tr>
                                    <td>{{ $pegawai->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $p->name }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $p->email }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" onclick="showQRCode('{{ $p->qrcode }}', '{{ $p->name }}')">
                                            <i class="fas fa-qrcode me-1"></i>Lihat QR
                                        </button>
                                    </td>
                                    <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.pegawai.edit', $p->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.jadwal.create', $p->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-calendar"></i>
                                            </a>
                                            <form action="{{ route('admin.pegawai.delete', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada data pegawai</p>
                                        <a href="{{ route('admin.pegawai.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Tambah Pegawai Pertama
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($pegawai->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $pegawai->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode me-2"></i>QR Code - <span id="qr-employee-name"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qr-code-container" class="mb-3"></div>
                <div class="alert alert-info">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        QR Code ini digunakan untuk absensi pegawai
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printQRCode()">
                    <i class="fas fa-print me-2"></i>Cetak
                </button>
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

@media print {
    body * {
        visibility: hidden;
    }
    #qr-code-container, #qr-code-container * {
        visibility: visible;
    }
    #qr-code-container {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
function showQRCode(qrcode, name) {
    $('#qr-employee-name').text(name);
    
    // Clear previous QR code
    $('#qr-code-container').empty();
    
    // Generate QR code
    QRCode.toCanvas(qrcode, { width: 200 }, function (error, canvas) {
        if (error) {
            console.error(error);
            $('#qr-code-container').html('<p class="text-danger">Error generating QR code</p>');
        } else {
            $('#qr-code-container').append(canvas);
        }
    });
    
    $('#qrModal').modal('show');
}

function printQRCode() {
    window.print();
}
</script>
@endpush
