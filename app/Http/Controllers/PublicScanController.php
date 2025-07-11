<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use App\Models\JadwalKerja;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PublicScanController extends Controller
{
    public function showScanner()
    {
        return view('public.scanner');
    }

    public function processAttendance(Request $request)
    {
        $request->validate([
            'qrcode' => 'required|string',
        ]);

        try {
            // Find user by QR code
            $user = User::where('qrcode', $request->qrcode)
                       ->where('role', 'pegawai')
                       ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak valid atau bukan milik pegawai aktif'
                ]);
            }

            $today = Carbon::now()->format('Y-m-d');
            $currentTime = Carbon::now()->format('H:i:s');
            $hariIni = $this->getHariIndonesia(Carbon::now()->dayOfWeek);

            // Check existing attendance today
            $absensiHariIni = Absensi::where('user_id', $user->id)
                                    ->whereDate('tanggal', $today)
                                    ->first();

            // Get today's work schedule
            $jadwalHariIni = JadwalKerja::where('id_user', $user->id)
                                       ->where('hari', $hariIni)
                                       ->first();

            if (!$jadwalHariIni) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada jadwal kerja untuk hari ini',
                    'employee_name' => $user->name
                ]);
            }

            if ($jadwalHariIni->is_libur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hari ini adalah hari libur',
                    'employee_name' => $user->name
                ]);
            }

            if (!$absensiHariIni) {
                // Clock in
                $status = 'hadir';
                if ($currentTime > $jadwalHariIni->jam_masuk) {
                    $status = 'terlambat';
                }

                Absensi::create([
                    'user_id' => $user->id,
                    'tanggal' => $today,
                    'jam_masuk' => $currentTime,
                    'status' => $status,
                ]);

                $statusText = $status === 'hadir' ? 'TEPAT WAKTU' : 'TERLAMBAT';

                return response()->json([
                    'success' => true,
                    'message' => "Absen MASUK berhasil - {$statusText}",
                    'type' => 'masuk',
                    'status' => $status,
                    'employee_name' => $user->name,
                    'time' => $currentTime
                ]);
            } else {
                // Clock out
                if ($absensiHariIni->jam_pulang) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda sudah absen pulang hari ini',
                        'employee_name' => $user->name
                    ]);
                }

                $absensiHariIni->update([
                    'jam_pulang' => $currentTime,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Absen PULANG berhasil',
                    'type' => 'pulang',
                    'employee_name' => $user->name,
                    'time' => $currentTime
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Public scan error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ]);
        }
    }

    private function getHariIndonesia($dayOfWeek)
    {
        $hari = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu'
        ];

        return $hari[$dayOfWeek];
    }
}
