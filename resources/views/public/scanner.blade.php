<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mesin Absensi QR Code</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .scanner-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            padding: 1rem 2rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header h1 {
            color: white;
            margin: 0;
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .header .time {
            color: rgba(255, 255, 255, 0.9);
            font-size: clamp(0.9rem, 2vw, 1.2rem);
            margin-top: 0.5rem;
            font-weight: 500;
        }

        .main-content {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 2rem;
            padding: 2rem;
            align-items: flex-start;
            justify-content: center;
        }

        .scanner-section {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .sidebar-section {
            width: 300px;
            position: sticky;
            top: 2rem;
        }

        .scanner-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 900px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .scanner-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .scanner-icon {
            font-size: clamp(2.5rem, 6vw, 4rem);
            color: #667eea;
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        .scanner-title {
            font-size: clamp(1.5rem, 4vw, 2rem);
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .scanner-subtitle {
            color: #666;
            font-size: clamp(0.9rem, 2vw, 1.1rem);
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .scanner-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: center;
        }

        .video-section {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .info-section {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .video-container {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        #scanner-video {
            width: 100%;
            height: auto;
            min-height: 280px;
            max-height: 350px;
            object-fit: cover;
            background: linear-gradient(45deg, #000, #333);
            border: none;
        }

        .video-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 200px;
            border: 3px solid #667eea;
            border-radius: 20px;
            pointer-events: none;
            animation: scanFrame 2s ease-in-out infinite;
        }

        @keyframes scanFrame {
            0%, 100% {
                border-color: #667eea;
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
            }
            50% {
                border-color: #764ba2;
                box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
            }
        }

        .scanner-status {
            padding: 1rem;
            border-radius: 12px;
            margin: 1rem 0;
            font-weight: 600;
            display: none;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .status-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-info {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .scanner-controls {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .btn-scanner {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 140px;
            justify-content: center;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn-scanner::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-scanner:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(220, 53, 69, 0.4);
        }

        .footer {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            padding: 1rem;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: auto;
        }

        .footer p {
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .recent-scans {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 1.5rem;
            backdrop-filter: blur(20px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            max-height: 600px;
            overflow-y: auto;
        }

        .recent-scans h6 {
            margin: 0 0 15px 0;
            color: #333;
            font-weight: 600;
            font-size: 1.1rem;
            border-bottom: 2px solid #667eea;
            padding-bottom: 8px;
        }

        .scan-item {
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 10px;
            font-size: 0.9rem;
            border-left: 4px solid;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .scan-item:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .scan-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-color: #28a745;
            color: #155724;
        }

        .scan-error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            border-color: #dc3545;
            color: #721c24;
        }

        .employee-info {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .employee-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .employee-details h6 {
            margin: 0;
            font-weight: 600;
            font-size: 1rem;
            color: #333;
            border: none;
            padding: 0;
        }

        .employee-details small {
            color: #666;
            font-size: 0.8rem;
        }

        .scan-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-masuk {
            background: #28a745;
            color: white;
        }

        .status-pulang {
            background: #dc3545;
            color: white;
        }

        .status-terlambat {
            background: #ffc107;
            color: #212529;
        }

        .scan-time {
            font-size: 0.8rem;
            color: #666;
            font-weight: 500;
        }

        .scan-animation {
            animation: slideInRight 0.5s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .scanner-result-overlay {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(15px);
            z-index: 1000;
            display: none;
            text-align: center;
            min-width: 300px;
        }

        .result-success {
            border: 3px solid #28a745;
        }

        .result-error {
            border: 3px solid #dc3545;
        }

        .result-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .result-success .result-icon {
            color: #28a745;
        }

        .result-error .result-icon {
            color: #dc3545;
        }

        /* Responsive Design */
        @media (min-width: 1200px) {
            .main-content {
                grid-template-columns: 1fr 400px;
                gap: 3rem;
                padding: 3rem;
            }

            .recent-scans {
                position: sticky;
                top: 2rem;
            }
        }

        @media (max-width: 1199px) {
            .main-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }

            .main-content {
                grid-template-columns: 1fr;
                padding: 1rem;
                gap: 1.5rem;
                align-items: center;
            }

            .sidebar-section {
                width: 100%;
                position: static;
                order: -1;
            }

            .scanner-section {
                min-height: auto;
                order: 1;
            }

            .scanner-card {
                padding: 1.5rem;
                border-radius: 20px;
                max-width: 100%;
            }

            .scanner-content {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .info-section {
                text-align: center;
                order: 1;
            }

            .video-section {
                order: 2;
            }

            .video-container {
                max-width: 100%;
            }

            #scanner-video {
                min-height: 220px;
                max-height: 300px;
            }

            .video-overlay {
                width: 150px;
                height: 150px;
            }

            .scanner-controls {
                flex-direction: column;
                gap: 0.75rem;
            }

            .btn-scanner {
                width: 100%;
                padding: 1rem;
                font-size: 1.1rem;
            }

            .recent-scans {
                padding: 1rem;
                border-radius: 16px;
                max-height: 300px;
            }

            .scan-item {
                padding: 1rem;
                border-radius: 12px;
            }

            .employee-avatar {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }

            .scanner-result-overlay {
                margin: 1rem;
                padding: 2rem;
                border-radius: 16px;
                min-width: auto;
                max-width: calc(100vw - 2rem);
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.5rem;
            }

            .header .time {
                font-size: 0.9rem;
            }

            .scanner-card {
                padding: 1rem;
                margin: 0;
            }

            .scanner-content {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .scanner-icon {
                font-size: 2.5rem;
            }

            .scanner-title {
                font-size: 1.3rem;
            }

            .scanner-subtitle {
                font-size: 0.9rem;
            }

            #scanner-video {
                min-height: 200px;
                max-height: 280px;
            }

            .video-overlay {
                width: 120px;
                height: 120px;
                border-width: 2px;
            }

            .btn-scanner {
                padding: 0.875rem;
                font-size: 1rem;
                min-width: auto;
            }

            .recent-scans h6 {
                font-size: 1rem;
            }

            .scan-item {
                padding: 0.75rem;
            }

            .employee-avatar {
                width: 30px;
                height: 30px;
                font-size: 0.9rem;
            }

            .employee-details h6 {
                font-size: 0.9rem;
            }

            .scanner-result-overlay {
                padding: 1.5rem;
            }

            .result-icon {
                font-size: 2.5rem;
            }
        }

        /* Smooth scrollbar */
        .recent-scans::-webkit-scrollbar {
            width: 6px;
        }

        .recent-scans::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 3px;
        }

        .recent-scans::-webkit-scrollbar-thumb {
            background: rgba(102, 126, 234, 0.5);
            border-radius: 3px;
        }

        .recent-scans::-webkit-scrollbar-thumb:hover {
            background: rgba(102, 126, 234, 0.7);
        }

        /* Loading animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Accessibility improvements */
        @media (prefers-reduced-motion: reduce) {
            .scanner-icon,
            .video-overlay,
            .scan-animation,
            .btn-scanner::before {
                animation: none;
            }

            .btn-scanner:hover {
                transform: none;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .scanner-card {
                background: rgba(30, 30, 30, 0.95);
                color: #fff;
            }

            .scanner-title {
                color: #fff;
            }

            .scanner-subtitle {
                color: #ccc;
            }

            .recent-scans {
                background: rgba(30, 30, 30, 0.95);
                color: #fff;
            }

            .recent-scans h6 {
                color: #fff;
                border-color: #667eea;
            }
        }
    </style>
</head>
<body>
    <div class="scanner-container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-qrcode me-3"></i>Mesin Absensi</h1>
            <div class="time" id="current-time"></div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Scanner Section - Main Content -->
            <div class="scanner-section">
                <div class="scanner-card">
                    <div class="scanner-content">
                        <!-- Video Section (Left Side) -->
                        <div class="video-section">
                            <div class="video-container">
                                <video id="scanner-video" autoplay muted playsinline></video>
                                <div class="video-overlay"></div>
                            </div>
                        </div>

                        <!-- Info Section (Right Side) -->
                        <div class="info-section">
                            <div class="scanner-icon">
                                <i class="fas fa-camera" id="scanner-icon"></i>
                            </div>

                            <h2 class="scanner-title">Scan QR Code Anda</h2>
                            <p class="scanner-subtitle">Arahkan QR Code pegawai ke kamera untuk melakukan absensi</p>

                            <div class="alert alert-info mb-3" style="background: linear-gradient(135deg, #d1ecf1, #bee5eb); border: 1px solid #bee5eb; border-radius: 12px; padding: 1rem;">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>QR Code berlaku selamanya</strong> - Gunakan QR Code yang sama setiap hari
                            </div>

                            <div class="scanner-status" id="scanner-status"></div>

                            <div class="scanner-controls">
                                <button class="btn-scanner btn-primary" id="start-scanner">
                                    <i class="fas fa-play"></i>
                                    <span>Mulai Scanner</span>
                                </button>
                                <button class="btn-scanner btn-danger" id="stop-scanner" style="display: none;">
                                    <i class="fas fa-stop"></i>
                                    <span>Stop Scanner</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Section - Recent Scans -->
            <div class="sidebar-section">
                <div class="recent-scans" id="recent-scans">
                    <h6><i class="fas fa-history me-2"></i>Absensi Terbaru</h6>
                    <div id="scan-history"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><i class="fas fa-shield-alt me-2"></i>Sistem Absensi QR Code - Secure & Reliable</p>
        </div>
    </div>



    <!-- Scan Result Overlay -->
    <div class="scanner-result-overlay" id="scan-result-overlay">
        <div class="result-icon" id="result-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h4 id="result-title">Absensi Berhasil!</h4>
        <p id="result-message">Selamat datang, John Doe</p>
        <div id="result-details"></div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- QR Scanner -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    
    <script>
        let scanner = null;
        let scanning = false;
        let scanHistory = [];

        // Update time
        function updateTime() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('current-time').textContent = now.toLocaleDateString('id-ID', options);
        }

        // Start scanner
        function startScanner() {
            const video = document.getElementById('scanner-video');
            const startBtn = document.getElementById('start-scanner');
            const stopBtn = document.getElementById('stop-scanner');
            const icon = document.getElementById('scanner-icon');

            // Show loading state
            startBtn.innerHTML = '<div class="loading-spinner"></div><span>Memulai...</span>';
            startBtn.disabled = true;

            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            })
            .then(function(stream) {
                video.srcObject = stream;
                video.play();
                scanning = true;
                scanner = stream;

                // Smooth transition
                startBtn.style.display = 'none';
                stopBtn.style.display = 'flex';
                icon.className = 'fas fa-video';

                showStatus('Scanner aktif - Arahkan QR Code ke kamera', 'info');
                requestAnimationFrame(tick);
            })
            .catch(function(err) {
                // Reset button state
                startBtn.innerHTML = '<i class="fas fa-play"></i><span>Mulai Scanner</span>';
                startBtn.disabled = false;

                showStatus('Error: Tidak dapat mengakses kamera - ' + err.message, 'error');
                console.error('Camera error:', err);
            });
        }

        // Stop scanner
        function stopScanner() {
            scanning = false;
            
            if (scanner) {
                scanner.getTracks().forEach(track => track.stop());
                scanner = null;
            }
            
            const video = document.getElementById('scanner-video');
            video.srcObject = null;
            
            const startBtn = document.getElementById('start-scanner');
            const stopBtn = document.getElementById('stop-scanner');
            const icon = document.getElementById('scanner-icon');
            
            startBtn.style.display = 'flex';
            stopBtn.style.display = 'none';
            icon.className = 'fas fa-camera';
            
            showStatus('Scanner dihentikan', 'info');
        }

        // Scanner tick
        function tick() {
            const video = document.getElementById('scanner-video');
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

        // Process QR Code
        function processQRCode(qrData) {
            showStatus('QR Code terdeteksi, memproses...', 'info');

            // Send AJAX request
            fetch('/public/scan-absensi', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    qrcode: qrData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showScanResult(data, 'success');
                    addToHistory(data, 'success');

                    // Auto restart scanner after 4 seconds
                    setTimeout(() => {
                        hideScanResult();
                        if (scanning) {
                            showStatus('Scanner aktif - Arahkan QR Code ke kamera', 'info');
                        }
                    }, 4000);
                } else {
                    showScanResult(data, 'error');
                    addToHistory(data, 'error');

                    // Auto restart scanner after 3 seconds
                    setTimeout(() => {
                        hideScanResult();
                        if (scanning) {
                            showStatus('Scanner aktif - Arahkan QR Code ke kamera', 'info');
                        }
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorData = {
                    success: false,
                    message: 'Terjadi kesalahan sistem',
                    employee_name: 'System Error'
                };
                showScanResult(errorData, 'error');
                addToHistory(errorData, 'error');
            });
        }

        // Show status
        function showStatus(message, type) {
            const status = document.getElementById('scanner-status');
            status.textContent = message;
            status.className = `scanner-status status-${type}`;
            status.style.display = 'block';
        }

        // Show scan result overlay
        function showScanResult(data, type) {
            const overlay = document.getElementById('scan-result-overlay');
            const icon = document.getElementById('result-icon');
            const title = document.getElementById('result-title');
            const message = document.getElementById('result-message');
            const details = document.getElementById('result-details');

            // Update overlay class
            overlay.className = `scanner-result-overlay result-${type}`;

            if (type === 'success') {
                icon.innerHTML = '<i class="fas fa-check-circle"></i>';
                title.textContent = data.type === 'masuk' ? 'Absen Masuk Berhasil!' : 'Absen Pulang Berhasil!';
                message.textContent = `Selamat ${data.type === 'masuk' ? 'datang' : 'jalan'}, ${data.employee_name}!`;

                let statusText = '';
                if (data.status === 'terlambat') {
                    statusText = '<span class="status-badge status-terlambat">Terlambat</span>';
                } else if (data.type === 'masuk') {
                    statusText = '<span class="status-badge status-masuk">Tepat Waktu</span>';
                }

                details.innerHTML = `
                    <div class="mt-3">
                        <p><strong>Waktu:</strong> ${data.time || new Date().toLocaleTimeString('id-ID')}</p>
                        ${statusText}
                    </div>
                `;
            } else {
                icon.innerHTML = '<i class="fas fa-times-circle"></i>';
                title.textContent = 'Absensi Gagal!';
                message.textContent = data.message;
                details.innerHTML = data.employee_name ? `<p><strong>Pegawai:</strong> ${data.employee_name}</p>` : '';
            }

            overlay.style.display = 'block';
        }

        // Hide scan result overlay
        function hideScanResult() {
            document.getElementById('scan-result-overlay').style.display = 'none';
        }

        // Add to history
        function addToHistory(data, type) {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID');

            scanHistory.unshift({
                data: data,
                type: type,
                time: timeStr,
                employee: data.employee_name || 'Unknown',
                scanType: data.type || 'unknown',
                status: data.status || 'unknown'
            });

            // Keep only last 8 scans
            if (scanHistory.length > 8) {
                scanHistory = scanHistory.slice(0, 8);
            }

            updateHistoryDisplay();
        }

        // Update history display
        function updateHistoryDisplay() {
            const historyDiv = document.getElementById('scan-history');
            historyDiv.innerHTML = '';

            scanHistory.forEach((item, index) => {
                const div = document.createElement('div');
                div.className = `scan-item scan-${item.type} scan-animation`;

                const initials = item.employee.split(' ').map(n => n[0]).join('').toUpperCase();

                let statusBadge = '';
                if (item.type === 'success') {
                    if (item.scanType === 'masuk') {
                        statusBadge = item.status === 'terlambat' ?
                            '<span class="status-badge status-terlambat">Terlambat</span>' :
                            '<span class="status-badge status-masuk">Masuk</span>';
                    } else {
                        statusBadge = '<span class="status-badge status-pulang">Pulang</span>';
                    }
                }

                div.innerHTML = `
                    <div class="employee-info">
                        <div class="employee-avatar">${initials}</div>
                        <div class="employee-details">
                            <h6>${item.employee}</h6>
                            <small>${item.data.message}</small>
                        </div>
                    </div>
                    <div class="scan-status">
                        <div class="scan-time">${item.time}</div>
                        ${statusBadge}
                    </div>
                `;

                historyDiv.appendChild(div);

                // Remove animation class after animation completes
                setTimeout(() => {
                    div.classList.remove('scan-animation');
                }, 500);
            });
        }

        // Event listeners
        document.getElementById('start-scanner').addEventListener('click', startScanner);
        document.getElementById('stop-scanner').addEventListener('click', stopScanner);

        // Initialize
        updateTime();
        setInterval(updateTime, 1000);

        // Auto start scanner on page load
        window.addEventListener('load', function() {
            setTimeout(startScanner, 1000);
        });

        // Handle page visibility change
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Page is hidden, stop scanner to save resources
                if (scanning) {
                    stopScanner();
                }
            } else {
                // Page is visible, restart scanner
                setTimeout(startScanner, 500);
            }
        });
    </script>
</body>
</html>
