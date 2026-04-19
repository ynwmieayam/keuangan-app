<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Toko Sembako')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #d4f4d4;
            min-height: 100vh;
        }
        
        /* Navbar */
        .navbar {
            background-color: #6b8e6b;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .logo {
            width: 90px;
            height: 90px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }
        .nav-menu {
            display: flex;
            gap: 15px;
            flex: 1;
        }
        .nav-menu a {
            background-color: white;
            color: #4a5d4a;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }
        .nav-menu a:hover,
        .nav-menu a.active {
            opacity: 0.8;
        }
        .logout-btn {
            background-color: transparent;
            border: 2px solid white;
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2em;
        }
        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Content */
        .container {
            padding: 30px 40px;
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @yield('additional-styles')
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <img src="{{ asset('images/logo-sembako.webp') }}" alt="Logo">
        </div>
        <div class="nav-menu">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('pemasukan.index') }}" class="{{ request()->routeIs('pemasukan.*') ? 'active' : '' }}">Pemasukan</a>
            <a href="{{ route('pengeluaran.index') }}" class="{{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}">Pengeluaran</a>
            <a href="{{ route('laporan.harian') }}" class="{{ request()->routeIs('laporan.harian') ? 'active' : '' }}">Laporan Harian</a>
            <a href="{{ route('laporan.bulanan') }}" class="{{ request()->routeIs('laporan.bulanan') ? 'active' : '' }}">Laporan Bulanan</a>
            <a href="{{ route('barang.index') }}" class="{{ request()->routeIs('barang.*') ? 'active' : '' }}">Data Barang</a>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="logout-btn">⇨</button>
        </form>
    </nav>

    <!-- Main Content -->
    @yield('content')

    <!-- Scripts -->
    @yield('scripts')
</body>
</html>