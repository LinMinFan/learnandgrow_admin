<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        $userList = User::all();

        return Inertia::render('Admin/Account/Index', compact('userList'));
    }
}
