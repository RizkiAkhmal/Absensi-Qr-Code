@extends('layouts.app')

@section('title', 'Atur Jadwal Massal')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">
                    <i class="fas fa-users-cog me-2"></i>Atur Jadwal Kerja Massal
                </h1>
                <a href="{{ route('admin.jadwal') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Set Jadwal untuk Multiple Pegawai
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.jadwal.bulk.store') }}" method="POST" id="bulkScheduleForm">
                        @csrf

                        @if($errors->any())
                            <div class="alert alert-danger mb-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Error:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Employee Selection -->
                        <div class="mb-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users me-2"></i>Pilih Pegawai
                                        </h6>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                                <i class="fas fa-check-double me-1"></i>Pilih Semua
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectNone()">
                                                <i class="fas fa-times me-1"></i>Batal Semua
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($pegawai as $p)
                                        <div class="col-md-4 col-lg-3 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input employee-checkbox" 
                                                       type="checkbox" 
                                                       name="user_ids[]" 
                                                       value="{{ $p->id }}" 
                                                       id="user_{{ $p->id }}">
                                                <label class="form-check-label" for="user_{{ $p->id }}">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            <i class="fas fa-user text-white"></i>
                                                        </div>
                                                        <div>
                                                            <strong>{{ $p->name }}</strong>
                                                            <br>
                                                            @if($p->jadwalKerja->count() > 0)
                                                                <small class="text-warning">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Sudah ada jadwal
                                                                </small>
                                                            @else
                                                                <small class="text-muted">Belum ada jadwal</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Info:</strong> Pegawai yang sudah memiliki jadwal akan diperbarui dengan jadwal baru.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Template -->
                        <div class="mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-calendar-week me-2"></i>Template Jadwal Kerja
                                        </h6>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="setWorkingDays()">
                                                <i class="fas fa-briefcase me-1"></i>Set Hari Kerja
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="setWeekend()">
                                                <i class="fas fa-calendar-times me-1"></i>Set Weekend
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @foreach($hari as $index => $h)
                                    <div class="card mb-3 schedule-card">
                                        <div class="card-header bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-calendar-day me-2"></i>{{ $h }}
                                                </h6>
                                                <div class="form-check">
                                                    <input type="hidden" name="schedules[{{ $index }}][is_libur]" value="0">
                                                    <input class="form-check-input holiday-checkbox"
                                                           type="checkbox"
                                                           id="is_libur_{{ $index }}"
                                                           name="schedules[{{ $index }}][is_libur]"
                                                           value="1"
                                                           onchange="toggleHoliday({{ $index }})">
                                                    <label class="form-check-label text-warning" for="is_libur_{{ $index }}">
                                                        <i class="fas fa-calendar-times me-1"></i>Libur
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <input type="hidden" name="schedules[{{ $index }}][hari]" value="{{ $h }}">
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Jam Masuk</label>
                                                        <input type="time" 
                                                               class="form-control time-input" 
                                                               id="jam_masuk_{{ $index }}" 
                                                               name="schedules[{{ $index }}][jam_masuk]" 
                                                               value="08:00">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Jam Pulang</label>
                                                        <input type="time" 
                                                               class="form-control time-input" 
                                                               id="jam_pulang_{{ $index }}" 
                                                               name="schedules[{{ $index }}][jam_pulang]" 
                                                               value="17:00">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="holiday-notice" id="holiday_notice_{{ $index }}" style="display: none;">
                                                <div class="alert alert-warning mb-0">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Hari ini ditetapkan sebagai hari libur
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Submit Section -->
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-check-circle me-2"></i>Siap untuk Diterapkan
                                        </h6>
                                        <p class="mb-0">
                                            Jadwal akan diterapkan ke <span id="selected-count">0</span> pegawai yang dipilih
                                        </p>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.jadwal') }}" class="btn btn-light me-2">
                                            <i class="fas fa-times me-2"></i>Batal
                                        </a>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-save me-2"></i>Terapkan Jadwal Massal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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

.schedule-card {
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.schedule-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.schedule-card .card-header {
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
}

.time-input:disabled {
    background-color: #f8f9fa;
    opacity: 0.6;
}

.holiday-notice {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.form-check-label {
    cursor: pointer;
}

.employee-checkbox:checked + .form-check-label {
    background-color: rgba(13, 110, 253, 0.1);
    border-radius: 8px;
    padding: 8px;
}
</style>
@endpush

@push('scripts')
<script>
function selectAll() {
    document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelectedCount();
}

function selectNone() {
    document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const selectedCount = document.querySelectorAll('.employee-checkbox:checked').length;
    document.getElementById('selected-count').textContent = selectedCount;
}

function toggleHoliday(index) {
    const checkbox = document.getElementById(`is_libur_${index}`);
    const jamMasuk = document.getElementById(`jam_masuk_${index}`);
    const jamPulang = document.getElementById(`jam_pulang_${index}`);
    const notice = document.getElementById(`holiday_notice_${index}`);
    
    if (checkbox.checked) {
        jamMasuk.disabled = true;
        jamPulang.disabled = true;
        jamMasuk.value = '';
        jamPulang.value = '';
        notice.style.display = 'block';
    } else {
        jamMasuk.disabled = false;
        jamPulang.disabled = false;
        jamMasuk.value = '08:00';
        jamPulang.value = '17:00';
        notice.style.display = 'none';
    }
}

function setWorkingDays() {
    // Set Senin-Jumat as working days
    for (let i = 0; i < 5; i++) {
        const checkbox = document.getElementById(`is_libur_${i}`);
        const jamMasuk = document.getElementById(`jam_masuk_${i}`);
        const jamPulang = document.getElementById(`jam_pulang_${i}`);
        
        checkbox.checked = false;
        jamMasuk.disabled = false;
        jamPulang.disabled = false;
        jamMasuk.value = '08:00';
        jamPulang.value = '17:00';
        document.getElementById(`holiday_notice_${i}`).style.display = 'none';
    }
}

function setWeekend() {
    // Set Sabtu-Minggu as holidays
    for (let i = 5; i < 7; i++) {
        const checkbox = document.getElementById(`is_libur_${i}`);
        const jamMasuk = document.getElementById(`jam_masuk_${i}`);
        const jamPulang = document.getElementById(`jam_pulang_${i}`);
        
        checkbox.checked = true;
        jamMasuk.disabled = true;
        jamPulang.disabled = true;
        jamMasuk.value = '';
        jamPulang.value = '';
        document.getElementById(`holiday_notice_${i}`).style.display = 'block';
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Update count on checkbox change
    document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Initial count
    updateSelectedCount();
    
    // Validate time inputs
    document.addEventListener('change', function(e) {
        if (e.target.type === 'time' && e.target.name.includes('jam_pulang')) {
            const index = e.target.name.match(/\[(\d+)\]/)[1];
            const jamMasuk = document.getElementById(`jam_masuk_${index}`).value;
            const jamPulang = e.target.value;
            
            if (jamMasuk && jamPulang && jamPulang <= jamMasuk) {
                alert('Jam pulang harus lebih besar dari jam masuk');
                e.target.value = '';
            }
        }
    });
});

// Form validation
document.getElementById('bulkScheduleForm').addEventListener('submit', function(e) {
    const selectedEmployees = document.querySelectorAll('.employee-checkbox:checked').length;
    
    if (selectedEmployees === 0) {
        e.preventDefault();
        alert('Pilih minimal satu pegawai untuk diatur jadwalnya');
        return;
    }
    
    let hasError = false;
    
    for (let i = 0; i < 7; i++) {
        const checkbox = document.getElementById(`is_libur_${i}`);
        const jamMasuk = document.getElementById(`jam_masuk_${i}`);
        const jamPulang = document.getElementById(`jam_pulang_${i}`);
        
        if (!checkbox.checked) {
            if (!jamMasuk.value || !jamPulang.value) {
                hasError = true;
                alert(`Jam masuk dan pulang harus diisi untuk hari kerja`);
                break;
            }
        }
    }
    
    if (hasError) {
        e.preventDefault();
    } else {
        // Confirmation
        if (!confirm(`Yakin ingin menerapkan jadwal ini ke ${selectedEmployees} pegawai? Jadwal yang sudah ada akan ditimpa.`)) {
            e.preventDefault();
        }
    }
});
</script>
@endpush
