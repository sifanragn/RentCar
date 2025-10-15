<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        return view('user.kontak.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:5',
        ]);

        ContactMessage::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'status'  => 'pending',
        ]);

        return back()->with('success', '✅ Pesan berhasil dikirim! Admin akan segera menanggapi.');
    }
}
