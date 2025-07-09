@extends('layouts.app')

@section('title', 'QR Code Absensi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">
                <i class="fas fa-qrcode me-2"></i>QR Code Absensi
            </h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>{{ $user->name }}
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <div class="qr-code-container p-4 bg-light rounded">
                            {!! QrCode::size(200)->generate($user->qrcode) !!}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted">QR Code ID:</h6>
                        <code class="fs-6">{{ $user->qrcode }}</code>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Cara Penggunaan:</strong>
                        <ul class="list-unstyled mt-2 mb-0">
                            <li>1. Tunjukkan QR Code ini ke scanner</li>
                            <li>2. Tunggu hingga proses scan selesai</li>
                            <li>3. Absensi akan tercatat secara otomatis</li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-success w-100" onclick="startScanner()">
                                <i class="fas fa-camera me-2"></i>Scan QR Code
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-outline-primary w-100" onclick="printQRCode()">
                                <i class="fas fa-print me-2"></i>Cetak QR Code
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scanner Modal -->
    <div class="modal fade" id="scannerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-camera me-2"></i>Scan QR Code untuk Absensi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="scanner-container" class="text-center">
                        <video id="scanner" width="100%" height="300" style="border: 1px solid #ddd;"></video>
                        <div class="mt-3">
                            <button class="btn btn-danger" onclick="stopScanner()">
                                <i class="fas fa-stop me-2"></i>Stop Scanner
                            </button>
                        </div>
                    </div>
                    <div id="scanner-result" class="mt-3" style="display: none;">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <span id="result-message"></span>
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
    .qr-code-container {
        display: inline-block;
        border: 2px solid #dee2e6;
    }
    
    @media print {
        body * {
            visibility: hidden;
        }
        .qr-code-container, .qr-code-container * {
            visibility: visible;
        }
        .qr-code-container {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
let scanner = null;
let scanning = false;

function startScanner() {
    $('#scannerModal').modal('show');
    
    const video = document.getElementById('scanner');
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(function(stream) {
            video.srcObject = stream;
            video.play();
            scanning = true;
            scanner = stream;
            
            requestAnimationFrame(tick);
        })
        .catch(function(err) {
            alert('Error accessing camera: ' + err.message);
        });
}

function tick() {
    const video = document.getElementById('scanner');
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    
    if (video.readyState === video.HAVE_ENOUGH_DATA && scanning) {
        canvas.height = video.videoHeight;
        canvas.width = video.videoWidth;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        
        if (code) {
            processQRCode(code.data);
            return;
        }
    }
    
    if (scanning) {
        requestAnimationFrame(tick);
    }
}

function processQRCode(qrData) {
    stopScanner();
    
    // Send AJAX request to process attendance
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.post('{{ route("pegawai.scan.absensi") }}', {
        qrcode: qrData
    })
    .done(function(response) {
        if (response.success) {
            $('#result-message').text(response.message);
            $('#scanner-result').show();
            
            setTimeout(function() {
                $('#scannerModal').modal('hide');
                location.reload();
            }, 2000);
        } else {
            alert('Error: ' + response.message);
        }
    })
    .fail(function() {
        alert('Terjadi kesalahan saat memproses absensi');
    });
}

function stopScanner() {
    scanning = false;
    
    if (scanner) {
        scanner.getTracks().forEach(track => track.stop());
        scanner = null;
    }
    
    const video = document.getElementById('scanner');
    video.srcObject = null;
}

function printQRCode() {
    window.print();
}

// Stop scanner when modal is closed
$('#scannerModal').on('hidden.bs.modal', function() {
    stopScanner();
});
</script>
@endpush
