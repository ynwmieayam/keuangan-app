<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        // Kalau sudah login, redirect ke dashboard
        if (Session::has('admin')) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        // Cari admin berdasarkan username
        $admin = Admin::where('username', $request->username)->first();

        // Cek apakah admin ditemukan dan password cocok (plain text)
        if ($admin && $request->password === $admin->password) {
            // Simpan data admin ke session
            Session::put('admin', [
                'id_admin' => $admin->id_admin,
                'username' => $admin->username,
            ]);

            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        // Login gagal
        return back()->withErrors([
            'login' => 'Username atau password salah',
        ])->withInput($request->only('username'));
    }

    /**
     * Proses logout
     */
    public function logout()
    {
        // Hapus session admin
        Session::forget('admin');
        Session::flush();

        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }

    /**
     * Middleware: Cek apakah user sudah login
     * Bisa dipanggil di route atau controller lain
     */
    public function checkAuth()
    {
        if (!Session::has('admin')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
    }
}