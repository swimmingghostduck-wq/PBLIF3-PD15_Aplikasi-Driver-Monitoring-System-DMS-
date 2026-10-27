<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                return $user->isAdmin()
                    ? redirect()->route('admin.dashboard')
                    : redirect()->route('driver.dashboard');
            }
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi pengguna (Admin atau Driver).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = trim($credentials['username']);
        $remember = $request->boolean('remember');

        // Backward compatibility alias: 'driver' -> akun utama 'apri'
        if (strtolower($username) === 'driver') {
            $user = User::where('username', 'apri')->first();
        } else {
            $user = User::where('username', $username)->first();
        }

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withInput($request->only('username'))
                ->with('danger', 'Username atau password yang Anda masukkan salah.');
        }

        if (!$user->is_active) {
            return back()->withInput($request->only('username'))
                ->with('danger', 'Akun Anda dinonaktifkan. Silakan hubungi administrator.');
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $intended = $request->session()->pull('url.intended');
        $defaultRoute = $user->isAdmin() ? route('admin.dashboard') : route('driver.dashboard');

        if (!$intended || str_contains($intended, '/login') || str_contains($intended, '/register') || str_contains($intended, '/logout')) {
            $targetUrl = $defaultRoute;
        } else {
            // Hindari pengemudi diarahkan ke area admin yang sebelumnya tersimpan di session
            if (!$user->isAdmin() && str_contains($intended, '/admin')) {
                $targetUrl = $defaultRoute;
            } else {
                $targetUrl = $intended;
            }
        }

        return redirect()->to($targetUrl)
            ->with('success', "Selamat datang kembali, {$user->full_name}!");
    }

    /**
     * Tampilkan formulir registrasi pengemudi.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                return $user->isAdmin()
                    ? redirect()->route('admin.dashboard')
                    : redirect()->route('driver.dashboard');
            }
        }

        return view('auth.register');
    }

    /**
     * Proses pendaftaran pengemudi baru.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users,username'],
            'full_name' => ['required', 'string', 'max:100'],
            'nim_nik' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'username.unique' => 'Username sudah digunakan, silakan pilih username lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus terdiri dari 6 karakter.',
        ]);

        User::create([
            'username' => trim($validated['username']),
            'full_name' => trim($validated['full_name']),
            'nim_nik' => isset($validated['nim_nik']) ? trim($validated['nim_nik']) : null,
            'password' => Hash::make($validated['password']),
            'role' => 'driver',
            'is_active' => true,
        ]);

        return redirect()->route('login')
            ->with('success', 'Pendaftaran akun Driver berhasil! Silakan masuk.');
    }

    /**
     * Logout pengguna dan hancurkan sesi.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil keluar dari sistem.');
    }
}

