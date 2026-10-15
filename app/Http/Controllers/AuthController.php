<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        // Akun nonaktif tidak boleh login (agent.md 4). Rate limit lewat middleware throttle di route.
        $ok = Auth::attempt(
            ['email' => $request->email, 'password' => $request->password, 'is_active' => true],
            $request->boolean('remember'),
        );

        if (! $ok) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah, atau akun tidak aktif.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.today'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function editPassword(): View
    {
        return view('auth.password');
    }

    public function updatePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $request->user()->update(['password' => $request->password, 'must_change_password' => false]);

        return redirect()->route('admin.today')->with('success', 'Password berhasil diganti.');
    }
}
