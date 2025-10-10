<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.index', [
            'admin_name' => session('admin_name'),
            'admin_role' => session('admin_role'),
        ]);
    }
}
