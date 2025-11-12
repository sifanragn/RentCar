<?php 
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\EmergencyNumber;

class EmergencyController extends Controller
{
    public function index()
    {
        $numbers = EmergencyNumber::all();
        return view('user.emergency.index', compact('numbers'));
    }
}
