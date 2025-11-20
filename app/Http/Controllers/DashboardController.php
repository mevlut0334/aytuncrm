<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Dashboard sayfasını göster
     */
    public function index()
    {
        return view('dashboard');
    }
}