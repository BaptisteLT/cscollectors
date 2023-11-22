<?php

namespace App\Http\Controllers\Auth;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogOutController extends Controller
{
    public function logout()
    {
        Session::flush();
        
        Auth::logout();

        return redirect()->route('homepage');
    }
}
