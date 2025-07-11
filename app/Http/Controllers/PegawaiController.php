<?php

namespace App\Http\Controllers;

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
