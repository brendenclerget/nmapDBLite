<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class APIController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Show the API management interface for a user
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('api.manage');
    }
}
