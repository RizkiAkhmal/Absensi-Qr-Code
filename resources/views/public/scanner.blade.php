<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mesin Absensi QR Code</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }
        
        .scanner-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header h1 {
            color: white;
            margin: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }
        
        .header .time {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.2rem;
            margin-top: 10px;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        
        .scanner-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        
        .scanner-icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .scanner-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .scanner-subtitle {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        #scanner-video {
            width: 100%;
            max-width: 400px;
            height: 300px;
            border: 3px solid #667eea;
            border-radius: 15px;
            background: #000;
            margin-bottom: 20px;
        }
        
        .scanner-status {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
            display: none;
        }
        
        .status-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .scanner-controls {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-scanner {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .footer {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 15px;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .footer p {
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
            font-size: 0.9rem;
        }
        
        .recent-scans {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 15px;
            max-width: 300px;
            max-height: 400px;
            overflow-y: auto;
            backdrop-filter: blur(10px);
        }
        
        .recent-scans h6 {
            margin: 0 0 10px 0;
            color: #333;
            font-weight: 600;
        }
        
        .scan-item {
            padding: 8px;
            margin-bottom: 5px;
            border-radius: 5px;
            font-size: 0.85rem;
            border-left: 3px solid;
        }
        
        .scan-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        .scan-error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .scanner-card {
                padding: 20px;
                margin: 20px;
            }
            
            .scanner-title {
                font-size: 1.5rem;
            }
            
            .recent-scans {
                position: relative;
                top: auto;
                right: auto;
                margin-top: 20px;
                max-width: 100%;
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
            <div class="scanner-card">
                <div class="scanner-icon">
                    <i class="fas fa-camera" id="scanner-icon"></i>
                </div>
                
                <h2 class="scanner-title">Scan QR Code Anda</h2>
                <p class="scanner-subtitle">Arahkan QR Code ke kamera untuk melakukan absensi</p>
                
                <div class="scanner-status" id="scanner-status"></div>
                
                <video id="scanner-video" autoplay muted playsinline></video>
                
                <div class="scanner-controls">
                    <button class="btn-scanner btn-primary" id="start-scanner">
                        <i class="fas fa-play"></i>
                        Mulai Scanner
                    </button>
                    <button class="btn-scanner btn-danger" id="stop-scanner" style="display: none;">
                        <i class="fas fa-stop"></i>
                        Stop Scanner
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><i class="fas fa-shield-alt me-2"></i>Sistem Absensi QR Code - Secure & Reliable</p>
        </div>
    </div>

    <!-- Recent Scans Panel -->
    <div class="recent-scans" id="recent-scans">
        <h6><i class="fas fa-history me-2"></i>Scan Terbaru</h6>
        <div id="scan-history"></div>
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
            
            navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'environment',
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                } 
            })
            .then(function(stream) {
                video.srcObject = stream;
                video.play();
                scanning = true;
                scanner = stream;
                
                startBtn.style.display = 'none';
                stopBtn.style.display = 'flex';
                icon.className = 'fas fa-camera-retro';
                
                showStatus('Scanner aktif - Arahkan QR Code ke kamera', 'info');
                requestAnimationFrame(tick);
            })
            .catch(function(err) {
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
                    showStatus(`✅ ${data.message}`, 'success');
                    addToHistory(data.message, 'success', data.employee_name || 'Unknown');
                    
                    // Auto restart scanner after 3 seconds
                    setTimeout(() => {
                        if (scanning) {
                            showStatus('Scanner aktif - Arahkan QR Code ke kamera', 'info');
                        }
                    }, 3000);
                } else {
                    showStatus(`❌ ${data.message}`, 'error');
                    addToHistory(data.message, 'error');
                    
                    // Auto restart scanner after 2 seconds
                    setTimeout(() => {
                        if (scanning) {
                            showStatus('Scanner aktif - Arahkan QR Code ke kamera', 'info');
                        }
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatus('❌ Terjadi kesalahan sistem', 'error');
                addToHistory('Kesalahan sistem', 'error');
            });
        }

        // Show status
        function showStatus(message, type) {
            const status = document.getElementById('scanner-status');
            status.textContent = message;
            status.className = `scanner-status status-${type}`;
            status.style.display = 'block';
        }

        // Add to history
        function addToHistory(message, type, employeeName = '') {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID');
            
            scanHistory.unshift({
                message: message,
                type: type,
                time: timeStr,
                employee: employeeName
            });
            
            // Keep only last 10 scans
            if (scanHistory.length > 10) {
                scanHistory = scanHistory.slice(0, 10);
            }
            
            updateHistoryDisplay();
        }

        // Update history display
        function updateHistoryDisplay() {
            const historyDiv = document.getElementById('scan-history');
            historyDiv.innerHTML = '';
            
            scanHistory.forEach(item => {
                const div = document.createElement('div');
                div.className = `scan-item scan-${item.type}`;
                div.innerHTML = `
                    <div style="font-weight: 600;">${item.employee}</div>
                    <div>${item.message}</div>
                    <small>${item.time}</small>
                `;
                historyDiv.appendChild(div);
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
