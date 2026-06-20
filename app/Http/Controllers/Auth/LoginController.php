<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.login', ['isAdmin' => false]);
    }

    public function showAdminLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.login', ['isAdmin' => true]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectByRole();
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
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }

    private function redirectByRole()
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('peserta.dashboard');
    }
}
