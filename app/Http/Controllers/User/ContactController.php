<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Menampilkan halaman kontak
     * Berisi form untuk user mengirim pesan ke admin
     */
    public function index()
    {
        return view('user.kontak.index');
    }

    /**
     * Menyimpan pesan dari user ke database
     */
    public function store(Request $request)
    {
        // ================= VALIDASI INPUT =================
        // subject: opsional (boleh kosong), maksimal 255 karakter
        // message: wajib diisi, minimal 5 karakter
        $request->validate([
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:5',
        ]);

        // ================= SIMPAN DATA =================
        // Data pesan disimpan ke tabel contact_messages
        ContactMessage::create([
            'user_id' => Auth::id(), // ID user yang sedang login
            'subject' => $request->subject,
            'message' => $request->message,
            'status'  => 'pending', // status awal (menunggu respon admin)
        ]);

        // ================= RESPONSE =================
        // Kembali ke halaman sebelumnya dengan notifikasi sukses
        return back()->with(
            'success',
            '✅ Pesan berhasil dikirim! Admin akan segera menanggapi.'
        );
    }
}