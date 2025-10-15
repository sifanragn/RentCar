<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;

class ContactAdminController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::with('user')->latest()->get();
        return view('admin.kontak.index', compact('messages'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|min:3',
        ]);

        $msg = ContactMessage::findOrFail($id);
        $msg->update([
            'reply' => $request->reply,
            'status' => 'replied',
            'admin_id' => Auth::id(),
        ]);

        return back()->with('success', '✅ Balasan berhasil dikirim!');
    }
}
