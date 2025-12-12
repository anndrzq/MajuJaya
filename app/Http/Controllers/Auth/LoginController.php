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
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->input('remember_me');


        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect('userData');
        }

        if (Auth::viaRemember()) {
            return redirect('userData');
        }

        return back()->with('error', 'The provided credentials do not match our records.');
    }
}
