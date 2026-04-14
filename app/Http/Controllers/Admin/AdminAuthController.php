<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Display the super admin login page.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.login');
    }
}
