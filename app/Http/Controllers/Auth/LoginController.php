<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    public function index()
    {
        return view('content.auth.index');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->input('remember_me');


        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect('dashboard');
        }

        if (Auth::viaRemember()) {
            return redirect('dashboard');
        }

        return back()->with('error', 'Silakan Periksa Kembali Email dan Password Anda.');
    }
}
