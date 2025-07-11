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
                            <li>1. Tunjukkan QR Code ini ke mesin scanner absensi</li>
                            <li>2. QR Code ini berlaku selamanya untuk absensi Anda</li>
                            <li>3. Gunakan di mesin scanner yang tersedia di kantor</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Penting:</strong> QR Code ini adalah identitas unik Anda. Jangan berikan kepada orang lain!
                    </div>
                        <button class="btn btn-outline-primary w-100" onclick="printQRCode()">
                            <i class="fas fa-print me-2"></i>Cetak QR Code
                        </button>
                    
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
<script>
function printQRCode() {
    window.print();
}
</script>
@endpush
