<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminSocialController extends Controller
{
    // Halaman daftar user
    public function index()
    {
        $users = User::orderBy('user_id', 'desc')->get();

        return view('admin.social-media.index', compact('users'));
    }

    // Detail user (profil + sosial media)
    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('admin.social-media.show', compact('user'));
    }
}
