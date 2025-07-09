<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use App\Models\JadwalKerja;
use Carbon\Carbon;

class PegawaiController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $absensiHariIni = Absensi::where('user_id', $user->id)
                                ->whereDate('tanggal', today())
                                ->first();

        $hariIni = $this->getHariIndonesia(Carbon::now()->dayOfWeek);
        $jadwalHariIni = JadwalKerja::where('id_user', $user->id)
                                   ->where('hari', $hariIni)
                                   ->first();

        return view('pegawai.dashboard', compact('absensiHariIni', 'jadwalHariIni'));
    }

    public function showQRCode()
    {
        $user = auth()->user();
        return view('pegawai.qrcode', compact('user'));
    }

    public function absensi()
    {
        $user = auth()->user();
        $absensi = Absensi::where('user_id', $user->id)
                          ->orderBy('tanggal', 'desc')
                          ->paginate(10);

        return view('pegawai.absensi.index', compact('absensi'));
    }

    public function scanAbsensi(Request $request)
    {
        $request->validate([
            'qrcode' => 'required',
        ]);

        $user = User::where('qrcode', $request->qrcode)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid']);
        }

        $today = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i:s');

        // Cek apakah sudah absen hari ini
        $absensiHariIni = Absensi::where('user_id', $user->id)
                                ->whereDate('tanggal', $today)
                                ->first();

        // Ambil jadwal kerja hari ini
        $hariIni = $this->getHariIndonesia(Carbon::now()->dayOfWeek);
        $jadwalHariIni = JadwalKerja::where('id_user', $user->id)
                                   ->where('hari', $hariIni)
                                   ->first();

        if (!$jadwalHariIni) {
            return response()->json(['success' => false, 'message' => 'Tidak ada jadwal kerja hari ini']);
        }

        if ($jadwalHariIni->is_libur) {
            return response()->json(['success' => false, 'message' => 'Hari ini adalah hari libur']);
        }

        if (!$absensiHariIni) {
            // Absen masuk
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

            return response()->json([
                'success' => true,
                'message' => 'Absen masuk berhasil',
                'type' => 'masuk',
                'status' => $status
            ]);
        } else {
            // Absen pulang
            if ($absensiHariIni->jam_pulang) {
                return response()->json(['success' => false, 'message' => 'Anda sudah absen pulang hari ini']);
            }

            $absensiHariIni->update([
                'jam_pulang' => $currentTime,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absen pulang berhasil',
                'type' => 'pulang'
            ]);
        }
    }

    public function jadwal()
    {
        $user = auth()->user();
        $jadwal = JadwalKerja::where('id_user', $user->id)->get();
        return view('pegawai.jadwal.index', compact('jadwal'));
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
