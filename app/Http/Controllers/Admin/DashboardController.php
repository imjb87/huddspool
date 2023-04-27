<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard');
    }
}