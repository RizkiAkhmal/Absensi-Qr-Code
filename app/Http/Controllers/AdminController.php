<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use App\Models\JadwalKerja;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPegawai = User::where('role', 'pegawai')->count();
        $absensiHariIni = Absensi::whereDate('tanggal', today())->count();
        $terlambatHariIni = Absensi::whereDate('tanggal', today())
                                  ->where('status', 'terlambat')->count();

        // Absensi terbaru (5 terakhir)
        $absensiTerbaru = Absensi::with('user')
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();

        // Statistik absensi 7 hari terakhir
        $statistikAbsensi = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $statistikAbsensi[] = [
                'tanggal' => $tanggal->format('Y-m-d'),
                'hari' => $tanggal->locale('id')->dayName,
                'hadir' => Absensi::whereDate('tanggal', $tanggal)->where('status', 'hadir')->count(),
                'terlambat' => Absensi::whereDate('tanggal', $tanggal)->where('status', 'terlambat')->count(),
                'alpha' => Absensi::whereDate('tanggal', $tanggal)->where('status', 'alpha')->count(),
            ];
        }

        return view('admin.dashboard', compact(
            'totalPegawai',
            'absensiHariIni',
            'terlambatHariIni',
            'absensiTerbaru',
            'statistikAbsensi'
        ));
    }

    public function pegawai()
    {
        $pegawai = User::where('role', 'pegawai')->paginate(10);
        return view('admin.pegawai.index', compact('pegawai'));
    }

    public function createPegawai()
    {
        return view('admin.pegawai.create');
    }

    public function storePegawai(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pegawai',
            'qrcode' => Str::uuid(),
        ]);

        return redirect()->route('admin.pegawai')->with('success', 'Pegawai berhasil ditambahkan');
    }

    public function editPegawai(User $user)
    {
        return view('admin.pegawai.edit', compact('user'));
    }

    public function updatePegawai(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.pegawai')->with('success', 'Pegawai berhasil diupdate');
    }

    public function deletePegawai(User $user)
    {
        $user->delete();
        return redirect()->route('admin.pegawai')->with('success', 'Pegawai berhasil dihapus');
    }

    public function jadwalKerja()
    {
        $pegawai = User::where('role', 'pegawai')->with('jadwalKerja')->get();
        return view('admin.jadwal.index', compact('pegawai'));
    }

    public function createJadwal($userId)
    {
        $user = User::findOrFail($userId);
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Get existing schedules
        $existingSchedules = JadwalKerja::where('id_user', $userId)->get()->keyBy('hari');

        return view('admin.jadwal.create', compact('user', 'hari', 'existingSchedules'));
    }

    public function storeJadwal(Request $request)
    {
        try {
            $request->validate([
                'id_user' => 'required|exists:users,id',
                'schedules' => 'required|array',
                'schedules.*.hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                'schedules.*.jam_masuk' => 'nullable|date_format:H:i',
                'schedules.*.jam_pulang' => 'nullable|date_format:H:i',
                'schedules.*.is_libur' => 'nullable|in:0,1',
            ]);

            // Delete existing schedules for this user
            JadwalKerja::where('id_user', $request->id_user)->delete();

            $createdCount = 0;

            // Create new schedules
            foreach ($request->schedules as $schedule) {
                if (!empty($schedule['hari'])) {
                    $isLibur = isset($schedule['is_libur']) && $schedule['is_libur'] == '1';

                    // Validate working day has time
                    if (!$isLibur && (empty($schedule['jam_masuk']) || empty($schedule['jam_pulang']))) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['schedules' => "Jam masuk dan pulang harus diisi untuk hari kerja: {$schedule['hari']}"]);
                    }

                    JadwalKerja::create([
                        'id_user' => $request->id_user,
                        'hari' => $schedule['hari'],
                        'jam_masuk' => $isLibur ? null : $schedule['jam_masuk'],
                        'jam_pulang' => $isLibur ? null : $schedule['jam_pulang'],
                        'is_libur' => $isLibur,
                    ]);

                    $createdCount++;
                }
            }

            if ($createdCount == 0) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['schedules' => 'Tidak ada jadwal yang berhasil disimpan']);
            }

            return redirect()->route('admin.jadwal')
                ->with('success', "Jadwal kerja berhasil disimpan untuk {$createdCount} hari");

        } catch (\Exception $e) {
            Log::error('Error storing schedule: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan jadwal: ' . $e->getMessage()]);
        }
    }

    public function copyJadwal(Request $request)
    {
        $request->validate([
            'from_user_id' => 'required|exists:users,id',
            'to_user_ids' => 'required|array',
            'to_user_ids.*' => 'exists:users,id',
        ]);

        $sourceSchedules = JadwalKerja::where('id_user', $request->from_user_id)->get();

        if ($sourceSchedules->isEmpty()) {
            return redirect()->back()->with('error', 'Pegawai sumber tidak memiliki jadwal kerja');
        }

        $copiedCount = 0;
        foreach ($request->to_user_ids as $userId) {
            // Delete existing schedules
            JadwalKerja::where('id_user', $userId)->delete();

            // Copy schedules
            foreach ($sourceSchedules as $schedule) {
                JadwalKerja::create([
                    'id_user' => $userId,
                    'hari' => $schedule->hari,
                    'jam_masuk' => $schedule->jam_masuk,
                    'jam_pulang' => $schedule->jam_pulang,
                    'is_libur' => $schedule->is_libur,
                ]);
            }
            $copiedCount++;
        }

        return redirect()->route('admin.jadwal')->with('success', "Jadwal berhasil disalin ke {$copiedCount} pegawai");
    }

    public function bulkCreateJadwal()
    {
        $pegawai = User::where('role', 'pegawai')->get();
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('admin.jadwal.bulk-create', compact('pegawai', 'hari'));
    }

    public function bulkStoreJadwal(Request $request)
    {
        try {
            $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
                'schedules' => 'required|array',
                'schedules.*.hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                'schedules.*.jam_masuk' => 'nullable|date_format:H:i',
                'schedules.*.jam_pulang' => 'nullable|date_format:H:i',
                'schedules.*.is_libur' => 'nullable|in:0,1',
            ]);

            // Validate schedule template
            foreach ($request->schedules as $schedule) {
                if (!empty($schedule['hari'])) {
                    $isLibur = isset($schedule['is_libur']) && $schedule['is_libur'] == '1';

                    if (!$isLibur && (empty($schedule['jam_masuk']) || empty($schedule['jam_pulang']))) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['schedules' => "Jam masuk dan pulang harus diisi untuk hari kerja: {$schedule['hari']}"]);
                    }
                }
            }

            $createdCount = 0;
            $updatedCount = 0;

            foreach ($request->user_ids as $userId) {
                // Check if user already has schedules
                $existingSchedules = JadwalKerja::where('id_user', $userId)->count();

                if ($existingSchedules > 0) {
                    $updatedCount++;
                    // Delete existing schedules
                    JadwalKerja::where('id_user', $userId)->delete();
                } else {
                    $createdCount++;
                }

                // Create new schedules
                foreach ($request->schedules as $schedule) {
                    if (!empty($schedule['hari'])) {
                        $isLibur = isset($schedule['is_libur']) && $schedule['is_libur'] == '1';

                        JadwalKerja::create([
                            'id_user' => $userId,
                            'hari' => $schedule['hari'],
                            'jam_masuk' => $isLibur ? null : $schedule['jam_masuk'],
                            'jam_pulang' => $isLibur ? null : $schedule['jam_pulang'],
                            'is_libur' => $isLibur,
                        ]);
                    }
                }
            }

            $message = "Jadwal berhasil diatur untuk " . count($request->user_ids) . " pegawai";
            if ($updatedCount > 0) {
                $message .= " ({$updatedCount} diperbarui, {$createdCount} baru)";
            }

            return redirect()->route('admin.jadwal')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error bulk storing schedule: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan jadwal: ' . $e->getMessage()]);
        }
    }

    public function laporan()
    {
        $absensi = Absensi::with('user')
                          ->orderBy('tanggal', 'desc')
                          ->paginate(20);
        return view('admin.laporan.index', compact('absensi'));
    }

    public function laporanFilter(Request $request)
    {
        $query = Absensi::with('user');

        if ($request->tanggal_mulai) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->tanggal_selesai) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->paginate(20);
        $pegawai = User::where('role', 'pegawai')->get();

        return view('admin.laporan.index', compact('absensi', 'pegawai'));
    }

    public function generateQR(Request $request)
    {
        $data = $request->input('data');

        if (!$data) {
            return response('No data provided', 400);
        }

        try {
            // Generate simple SVG QR code placeholder
            $size = 180;
            $cellSize = $size / 25; // 25x25 grid

            $svg = '<svg width="' . $size . '" height="' . $size . '" xmlns="http://www.w3.org/2000/svg">';
            $svg .= '<rect width="' . $size . '" height="' . $size . '" fill="white"/>';

            // Create a simple pattern based on data hash
            $hash = md5($data);
            for ($i = 0; $i < 25; $i++) {
                for ($j = 0; $j < 25; $j++) {
                    $index = ($i * 25 + $j) % strlen($hash);
                    if (hexdec($hash[$index]) % 2 == 0) {
                        $x = $j * $cellSize;
                        $y = $i * $cellSize;
                        $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $cellSize . '" height="' . $cellSize . '" fill="black"/>';
                    }
                }
            }

            // Add corner markers
            $cornerSize = $cellSize * 7;
            $corners = [
                [0, 0], [18 * $cellSize, 0], [0, 18 * $cellSize]
            ];

            foreach ($corners as $corner) {
                $svg .= '<rect x="' . $corner[0] . '" y="' . $corner[1] . '" width="' . $cornerSize . '" height="' . $cornerSize . '" fill="black"/>';
                $svg .= '<rect x="' . ($corner[0] + $cellSize) . '" y="' . ($corner[1] + $cellSize) . '" width="' . ($cornerSize - 2 * $cellSize) . '" height="' . ($cornerSize - 2 * $cellSize) . '" fill="white"/>';
                $svg .= '<rect x="' . ($corner[0] + 2 * $cellSize) . '" y="' . ($corner[1] + 2 * $cellSize) . '" width="' . ($cornerSize - 4 * $cellSize) . '" height="' . ($cornerSize - 4 * $cellSize) . '" fill="black"/>';
            }

            $svg .= '</svg>';

            return response($svg)->header('Content-Type', 'image/svg+xml');
        } catch (\Exception $e) {
            Log::error('QR Generation Error: ' . $e->getMessage());
            return response('QR Generation Failed', 500);
        }
    }
}
