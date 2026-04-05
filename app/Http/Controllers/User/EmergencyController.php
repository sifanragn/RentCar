<?php 

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\EmergencyNumber;

class EmergencyController extends Controller
{
    /**
     * Menampilkan daftar nomor darurat
     * Data diambil dari database (tabel emergency_numbers)
     */
    public function index()
    {
        // ================= AMBIL DATA =================
        // Mengambil seluruh data nomor darurat
        $numbers = EmergencyNumber::all();

        // ================= TAMPILKAN VIEW =================
        // Mengirim data ke halaman user.emergency.index
        return view('user.emergency.index', compact('numbers'));
    }
}