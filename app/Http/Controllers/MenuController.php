<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {

        return Inertia::render('System/Menu');
    }
}
