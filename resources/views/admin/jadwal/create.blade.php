@extends('layouts.app')

@section('title', 'Atur Jadwal Kerja')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">
                    <i class="fas fa-calendar-plus me-2"></i>Atur Jadwal Kerja
                </h1>
                <a href="{{ route('admin.jadwal') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Jadwal Kerja untuk: {{ $user->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.jadwal.store') }}" method="POST" id="scheduleForm">
                        @csrf
                        <input type="hidden" name="id_user" value="{{ $user->id }}">

                        <div class="mb-4">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Error:</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Petunjuk:</strong> Atur jadwal kerja untuk semua hari dalam seminggu. Centang "Libur" untuk hari libur.
                            </div>
                        </div>

                        @foreach($hari as $index => $h)
                            @php
                                $existing = $existingSchedules->get($h);
                            @endphp
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
                                                   {{ ($existing && $existing->is_libur) ? 'checked' : '' }}
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
                                                       value="{{ $existing ? $existing->jam_masuk : '08:00' }}"
                                                       {{ ($existing && $existing->is_libur) ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Jam Pulang</label>
                                                <input type="time"
                                                       class="form-control time-input"
                                                       id="jam_pulang_{{ $index }}"
                                                       name="schedules[{{ $index }}][jam_pulang]"
                                                       value="{{ $existing ? $existing->jam_pulang : '17:00' }}"
                                                       {{ ($existing && $existing->is_libur) ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="holiday-notice" id="holiday_notice_{{ $index }}" style="{{ ($existing && $existing->is_libur) ? '' : 'display: none;' }}">
                                        <div class="alert alert-warning mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Hari ini ditetapkan sebagai hari libur
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Tips:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Atur semua jadwal sekaligus untuk efisiensi</li>
                                <li>Centang "Libur" untuk hari libur (Sabtu/Minggu biasanya)</li>
                                <li>Jam pulang harus lebih besar dari jam masuk</li>
                                <li>Jadwal yang sudah ada akan diperbarui</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="button" class="btn btn-outline-primary" onclick="setWorkingDays()">
                                    <i class="fas fa-briefcase me-2"></i>Set Hari Kerja (Sen-Jum)
                                </button>
                                <button type="button" class="btn btn-outline-warning" onclick="setWeekend()">
                                    <i class="fas fa-calendar-times me-2"></i>Set Weekend (Sab-Min)
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('admin.jadwal') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Semua Jadwal
                                </button>
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
</style>
@endpush

@push('scripts')
<script>
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

// Form validation
document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    let hasError = false;
    let errorMessage = '';

    // Debug: Log form data before submit
    console.log('Form data before submit:');
    const formData = new FormData(this);
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    for (let i = 0; i < 7; i++) {
        const checkbox = document.getElementById(`is_libur_${i}`);
        const jamMasuk = document.getElementById(`jam_masuk_${i}`);
        const jamPulang = document.getElementById(`jam_pulang_${i}`);
        const hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][i];

        if (!checkbox.checked) {
            if (!jamMasuk.value || !jamPulang.value) {
                hasError = true;
                errorMessage = `Jam masuk dan pulang harus diisi untuk hari kerja: ${hari}`;
                break;
            }

            if (jamPulang.value <= jamMasuk.value) {
                hasError = true;
                errorMessage = `Jam pulang harus lebih besar dari jam masuk untuk hari: ${hari}`;
                break;
            }
        }
    }

    if (hasError) {
        e.preventDefault();
        alert(errorMessage);
    }
});
</script>
@endpush
