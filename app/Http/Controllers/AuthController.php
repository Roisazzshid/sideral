<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isTeknisi()
                ? redirect()->route('maintenance')
                : redirect()->route('dashboard');
        }

        return view('pages.auth.signin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->isTeknisi()) {
                return redirect()->route('maintenance')->with('success', 'Selamat datang, ' . $user->name);
            }

            return redirect()->route('dashboard')->with('success', 'Selamat datang, ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('signin')->with('success', 'Anda telah berhasil keluar.');
    }
}
