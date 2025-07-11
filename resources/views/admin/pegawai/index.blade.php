@extends('layouts.app')

@section('title', 'Kelola Pegawai')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-users me-2 text-primary"></i>Kelola Pegawai
                    </h1>
                    <p class="text-muted mb-0">Manajemen data pegawai dan QR code</p>
                </div>
                <div>
                    <!-- DEBUG BUTTON -->
                    <a href="{{ route('admin.pegawai.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Tambah Pegawai
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="stats-summary">
        <div class="row">
            <div class="col-md-4">
                <div class="stats-item">
                    <div class="number">{{ $pegawai->total() }}</div>
                    <div class="label">Total Pegawai</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-item">
                    <div class="number">{{ $pegawai->count() }}</div>
                    <div class="label">Ditampilkan</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-item">
                    <div class="number">{{ $pegawai->currentPage() }}</div>
                    <div class="label">Halaman</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card table-modern">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Pegawai</th>
                                    <th>Email</th>
                                    <th>QR Code</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pegawai as $index => $p)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $pegawai->firstItem() + $index }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="employee-avatar me-3">
                                                {{ strtoupper(substr($p->name, 0, 1)) }}
                                            </div>
                                            <div class="employee-info">
                                                <h6 class="mb-0">{{ $p->name }}</h6>
                                                <small class="text-muted">ID: {{ $p->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $p->email }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm qr-button" onclick="showQRCode('{{ $p->qrcode }}', '{{ $p->name }}')">
                                            <i class="fas fa-qrcode me-1"></i>Lihat QR
                                        </button>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $p->created_at->format('d/m/Y') }}</small><br>
                                        <small class="text-muted">{{ $p->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.pegawai.edit', $p->id) }}" class="btn btn-sm btn-warning" title="Edit Pegawai">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.jadwal.create', $p->id) }}" class="btn btn-sm btn-info" title="Atur Jadwal">
                                                <i class="fas fa-calendar"></i>
                                            </a>
                                            <form action="{{ route('admin.pegawai.delete', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Pegawai">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode me-2"></i>QR Code Pegawai
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Preview Card -->
                        <div class="qr-card-container" id="qr-card-preview">
                            <div class="qr-card-header">
                                <div class="qr-card-title">
                                    <i class="fas fa-qrcode me-2"></i>KARTU ABSENSI
                                </div>
                                <div class="qr-card-subtitle">Sistem Absensi QR Code</div>
                            </div>

                            <div class="qr-code-section">
                                <div id="qr-code-container">
                                    <!-- FALLBACK QR CODE -->
                                    <div style="width:180px;height:180px;background:#f8f9fa;border:2px dashed #dee2e6;display:flex;align-items:center;justify-content:center;border-radius:10px;margin:0 auto;">
                                        <div style="text-align:center;color:#6c757d;">
                                            <i class="fas fa-qrcode fa-2x mb-2"></i><br>
                                            <small>Loading QR...</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="qr-code-label">Scan untuk Absensi</div>
                            </div>

                            <div class="employee-info-section">
                                <div class="employee-name" id="qr-employee-name"></div>
                                <div class="employee-id">
                                    <i class="fas fa-id-badge me-1"></i>ID: <span id="qr-employee-id"></span>
                                </div>

                        
                            </div>

                            <div class="company-footer">
                                <div class="mb-1"><i class="fas fa-building me-1"></i>PT. Sistem Absensi Digital</div>
                                <div><i class="fas fa-globe me-1"></i>www.absensi.com</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Kartu
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Nama Pegawai:</label>
                                    <div class="fw-bold" id="modal-employee-name"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ID Pegawai:</label>
                                    <div class="fw-bold" id="modal-employee-id"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">QR Code:</label>
                                    <div class="font-monospace small" id="modal-qr-code"></div>
                                </div>

                                <div class="alert alert-info">
                                    <small>
                                        <i class="fas fa-lightbulb me-1"></i>
                                        <strong>Tips:</strong> Kartu ini dapat dicetak dan diberikan kepada pegawai untuk kemudahan absensi.
                                    </small>
                                </div>

                                <div class="alert alert-warning">
                                    <small>
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        <strong>Penting:</strong> QR Code ini bersifat permanen dan unik untuk setiap pegawai.
                                    </small>
                                </div>

                                <div class="qr-instructions">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Scan QR code ini pada mesin absensi untuk melakukan check-in dan check-out
                                </div>

                                <div class="validity-info">
                                    <i class="fas fa-calendar-check me-1"></i>
                                    Berlaku Selamanya
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
                <button type="button" class="btn btn-primary" onclick="printQRCode()">
                    <i class="fas fa-print me-2"></i>Cetak Kartu
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
function showQRCode(qrcode, name) {
    console.log('=== SHOW QR CODE FUNCTION CALLED ===');
    console.log('QR Code:', qrcode);
    console.log('Name:', name);
    console.log('jQuery available:', typeof $);
    console.log('QRCode library:', typeof QRCode);

    try {
        // Get employee data from the row
        const employeeRow = event.target.closest('tr');
        const employeeIdElement = employeeRow.querySelector('.employee-info small');
        const employeeId = employeeIdElement ? employeeIdElement.textContent.replace('ID: ', '') : 'N/A';

        console.log('Employee ID:', employeeId);

        // Update modal content - Use both jQuery and vanilla JS
        if (typeof $ !== 'undefined') {
            // jQuery method
            $('#qr-employee-name').text(name);
            $('#qr-employee-id').text(employeeId);
            $('#modal-employee-name').text(name);
            $('#modal-employee-id').text(employeeId);
            $('#modal-qr-code').text(qrcode);
            $('#qr-code-container').empty();
        } else {
            // Vanilla JS fallback
            document.getElementById('qr-employee-name').textContent = name;
            document.getElementById('qr-employee-id').textContent = employeeId;
            document.getElementById('modal-employee-name').textContent = name;
            document.getElementById('modal-employee-id').textContent = employeeId;
            document.getElementById('modal-qr-code').textContent = qrcode;
            document.getElementById('qr-code-container').innerHTML = '';
        }

        // Generate QR code - PAKAI LARAVEL BACKEND DULU
        const container = document.getElementById('qr-code-container');
        console.log('Container found:', container);
        console.log('QR Code value:', qrcode);

        // METHOD 1: Laravel Backend QR Generation
        console.log('Generating QR with Laravel backend...');
        generateLaravelQR(qrcode, container);

        // METHOD 2: Fallback ke Google Charts setelah 2 detik
        setTimeout(function() {
            if (container.innerHTML.indexOf('Loading QR') !== -1 || container.innerHTML.indexOf('QR Generation Failed') !== -1) {
                console.log('Laravel failed, trying Google Charts...');
                generateGoogleQR(qrcode, container);
            }
        }, 2000);

        // METHOD 3: Final fallback ke QRCode.js setelah 4 detik
        setTimeout(function() {
            if (container.innerHTML.indexOf('QR Generation Failed') !== -1) {
                console.log('All methods failed, trying QRCode.js...');
                if (typeof QRCode !== 'undefined') {
                    QRCode.toCanvas(qrcode, {
                        width: 180,
                        margin: 2,
                        color: {
                            dark: '#000000',
                            light: '#FFFFFF'
                        }
                    }, function (error, canvas) {
                        if (!error && canvas) {
                            console.log('QRCode.js final backup successful');
                            canvas.style.borderRadius = '10px';
                            canvas.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                            container.innerHTML = '';
                            container.appendChild(canvas);
                        }
                    });
                }
            }
        }, 4000);

        // Show modal
        if (typeof $ !== 'undefined' && $.fn.modal) {
            $('#qrModal').modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(document.getElementById('qrModal'));
            modal.show();
        } else {
            // Fallback - just show the modal div
            const modal = document.getElementById('qrModal');
            modal.style.display = 'block';
            modal.classList.add('show');
            modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
        }

        console.log('Modal should be shown now');

        // IMMEDIATE QR GENERATION SETELAH MODAL MUNCUL
        setTimeout(function() {
            console.log('Force generating QR after modal shown...');
            const containerCheck = document.getElementById('qr-code-container');
            if (containerCheck && (containerCheck.innerHTML.indexOf('Loading QR') !== -1 || containerCheck.innerHTML.trim() === '')) {
                console.log('Container still empty/loading, forcing Google QR...');
                generateGoogleQR(qrcode, containerCheck);
            }
        }, 500);

    } catch (error) {
        console.error('Error in showQRCode function:', error);
        alert('Error: ' + error.message);
    }
}

function generateGoogleQR(qrcode, container) {
    console.log('Generating QR with Google Charts API...');
    console.log('QR Code value:', qrcode);

    // Coba beberapa URL format
    const qrUrls = [
        'https://chart.googleapis.com/chart?chs=180x180&cht=qr&chl=' + encodeURIComponent(qrcode),
        'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(qrcode),
        'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' + qrcode.replace(/[^a-zA-Z0-9-]/g, '')
    ];

    let currentUrlIndex = 0;

    function tryNextUrl() {
        if (currentUrlIndex >= qrUrls.length) {
            console.log('All QR services failed, using Laravel QR fallback');
            generateLaravelQR(qrcode, container);
            return;
        }

        const qrUrl = qrUrls[currentUrlIndex];
        console.log('Trying QR URL:', qrUrl);

        const img = new Image();
        img.onload = function() {
            console.log('QR loaded successfully from:', qrUrl);
            container.innerHTML = '';
            img.style.width = '180px';
            img.style.height = '180px';
            img.style.borderRadius = '10px';
            img.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
            container.appendChild(img);
        };
        img.onerror = function() {
            console.log('QR failed from:', qrUrl);
            currentUrlIndex++;
            setTimeout(tryNextUrl, 500);
        };
        img.src = qrUrl;
    }

    tryNextUrl();
}

function generateLaravelQR(qrcode, container) {
    console.log('Using Laravel QR generation...');

    // Buat request ke Laravel untuk generate QR
    const qrUrl = '/admin/generate-qr?data=' + encodeURIComponent(qrcode);
    console.log('Laravel QR URL:', qrUrl);

    fetch(qrUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'image/svg+xml,image/png,*/*',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        console.log('Laravel QR response status:', response.status);
        console.log('Laravel QR response headers:', response.headers.get('content-type'));

        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('image/svg+xml')) {
            return response.text();
        } else if (contentType && contentType.includes('image/png')) {
            return response.blob();
        } else {
            return response.text();
        }
    })
    .then(data => {
        console.log('Laravel QR generated successfully');
        container.innerHTML = '';

        if (typeof data === 'string') {
            // SVG response
            const wrapper = document.createElement('div');
            wrapper.style.cssText = 'display:flex;justify-content:center;align-items:center;width:180px;height:180px;border-radius:10px;box-shadow:0 4px 8px rgba(0,0,0,0.1);background:white;';
            wrapper.innerHTML = data;
            container.appendChild(wrapper);
        } else {
            // PNG blob response
            const img = document.createElement('img');
            img.src = URL.createObjectURL(data);
            img.style.cssText = 'width:180px;height:180px;border-radius:10px;box-shadow:0 4px 8px rgba(0,0,0,0.1);';
            container.appendChild(img);
        }
    })
    .catch(error => {
        console.log('Laravel QR failed:', error);
        container.innerHTML = '<div style="width:180px;height:180px;background:#f8f9fa;border:2px solid #dc3545;display:flex;align-items:center;justify-content:center;border-radius:10px;color:#dc3545;text-align:center;font-size:12px;"><div><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>Laravel QR Failed<br><small>' + error.message + '</small></div></div>';
    });
}

function printQRCode() {
    console.log('Print QR Code called');
    window.print();
}



// Debug on page load
$(document).ready(function() {
    console.log('=== PAGE LOADED ===');
    console.log('jQuery version:', $.fn.jquery);
    console.log('QRCode library:', typeof QRCode);
    console.log('Bootstrap modal:', typeof bootstrap);
    console.log('Modal element:', document.getElementById('qrModal'));

    // Test QR button clicks
    $('.qr-button').on('click', function(e) {
        console.log('QR button clicked via jQuery event');
        e.preventDefault();
    });
});
</script>
@endpush

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}

.employee-card {
    transition: all 0.3s ease;
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.employee-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.employee-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.2rem;
}

.employee-info h6 {
    margin-bottom: 0.25rem;
    font-weight: 600;
    color: #2c3e50;
}

.employee-info small {
    color: #6c757d;
}

.action-buttons .btn {
    margin: 0 2px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-1px);
}

.qr-button {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border: none;
    color: white;
}

.qr-button:hover {
    background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
    color: white;
}

.stats-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.stats-item {
    text-align: center;
}

.stats-item .number {
    font-size: 2rem;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 0.25rem;
}

.stats-item .label {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0;
}

.table-modern {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.table-modern thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 600;
    padding: 1rem;
}

.table-modern tbody td {
    padding: 1rem;
    border-color: #f8f9fa;
    vertical-align: middle;
}

.table-modern tbody tr:hover {
    background: #f8f9fa;
}

/* QR Card Design */
.qr-card-container {
    width: 350px;
    height: 550px;
    margin: 0 auto;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    color: white;
    position: relative;
    overflow: hidden;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.qr-card-container::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { transform: rotate(0deg); }
    50% { transform: rotate(180deg); }
}

.qr-card-header {
    text-align: center;
    margin-bottom: 30px;
    position: relative;
    z-index: 2;
}

.qr-card-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 5px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.qr-card-subtitle {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.qr-code-section {
    background: white;
    border-radius: 15px;
    padding: 20px;
    margin: 20px 0;
    text-align: center;
    position: relative;
    z-index: 2;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.qr-code-section canvas {
    border-radius: 10px;
}

.qr-code-label {
    color: #667eea;
    font-size: 0.8rem;
    font-weight: 600;
    margin-top: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.employee-info-section {
    text-align: center;
    position: relative;
    z-index: 2;
    margin-top: 20px;
    padding-bottom: 70px;
}

.employee-name {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.employee-id {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 20px;
    margin-top: 5px;
}

.qr-instructions {
    font-size: 0.8rem;
    opacity: 0.8;
    line-height: 1.4;
    margin-top: 15px;
    margin-bottom: 10px;
    padding: 12px 15px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    border-left: 3px solid rgba(255, 255, 255, 0.3);
    text-align: center;
}

.validity-info {
    font-size: 0.75rem;
    opacity: 0.9;
    margin-top: 15px;
    margin-bottom: 20px;
    padding: 8px 15px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 15px;
    display: inline-block;
    text-align: center;
}

.company-footer {
    position: absolute;
    bottom: 25px;
    left: 30px;
    right: 30px;
    text-align: center;
    font-size: 0.7rem;
    opacity: 0.7;
    z-index: 2;
    line-height: 1.6;
}

.company-footer div {
    margin-bottom: 4px;
}

.company-footer div:last-child {
    margin-bottom: 0;
}

@media print {
    @page {
        size: A4 portrait;
        margin: 0;
    }

    body * {
        visibility: hidden;
    }

    .qr-card-container, .qr-card-container * {
        visibility: visible;
    }

    .qr-card-container {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 350px;
        height: 550px;
        margin: 0;
        box-shadow: none;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    .modal, .modal-backdrop {
        display: none !important;
    }
}
</style>
@endpush
