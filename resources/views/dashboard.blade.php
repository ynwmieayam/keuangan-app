@extends('layouts.app')

@section('title', 'Dashboard - Toko Sembako')

@section('additional-styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<style>
    .welcome-text {
        color: #4a5d4a;
        font-family: 'Lato', sans-serif;
        font-size: 1.4em;
        font-weight: 400;
        margin-bottom: 20px;
    }
    .page-title {
        color: #4a5d4a;
        font-family: 'Playfair Display', serif;
        font-size: 2.5em;
        font-weight: 600;
        letter-spacing: 2px;
        margin-bottom: 6px;
        text-align: center;
    }
    .page-subtitle {
        text-align: center;
        color: #4a5d4a;
        font-family: 'Lato', sans-serif;
        font-size: 1em;
        letter-spacing: 1px;
        margin-bottom: 25px;
    }
    .main-content {
        display: flex;
        gap: 40px;
        align-items: center;
    }
    .left-section {
        flex: 0 0 400px;
        margin-left: 80px;
    }
    .right-section {
        flex: 1;
        display: flex;
        justify-content: flex-end;
        padding-right: 60px;
    }
    .info-boxes {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .info-box {
        background-color: white;
        padding: 22px 35px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
    }
    .info-box h3 {
        color: #4a5d4a;
        font-family: 'Lato', sans-serif;
        font-size: 1em;
        font-weight: 700;
        letter-spacing: 2px;
        margin-bottom: 8px;
    }
    .info-box .amount {
        color: #2d5a2d;
        font-family: 'Playfair Display', serif;
        font-size: 1.5em;
        font-weight: 600;
    }
    .info-box.profit .amount {
        color: #1a8a1a;
    }
    .illustration {
        width: 600px;
        height: 600px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .video-modal {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100vw; height: 100vh;
        background: rgba(0, 0, 0, 0.93);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .video-modal.active {
        display: flex;
    }
    .video-modal iframe {
        width: 90vw;
        height: 90vh;
        border: none;
        border-radius: 10px;
    }
    .video-close {
        position: absolute;
        top: 18px;
        right: 28px;
        background: none;
        border: none;
        color: white;
        font-size: 2.8em;
        cursor: pointer;
        line-height: 1;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    .video-close:hover { opacity: 1; }
    @media (max-width: 1024px) {
        .main-content { flex-direction: column; }
        .left-section { width: 100%; flex: unset; margin-left: 0; }
        .right-section { justify-content: center; padding-right: 0; }
        .illustration { width: 280px; height: 280px; }
    }
</style>
@endsection

@section('content')
<div class="container">

    {{-- Fullscreen Local Video Modal --}}
    <div class="video-modal" id="videoModal" onclick="closeVideo(event)">
        <button class="video-close" onclick="closeVideo()">&times;</button>
        <video id="localPlayer" controls autoplay style="width:90vw;height:90vh;border-radius:10px;outline:none;" onclick="event.stopPropagation()">
        <source src="" id="videoSource" type="video/mp4">
    </video>
    </div>

    <div class="welcome-text">
        Selamat Datang, {{ session('admin')['username'] ?? 'User' }}
    </div>

    <h1 class="page-title">Laporan Bulan Ini</h1>
    <p class="page-subtitle">{{ $namaBulan }}</p>

    <div class="main-content">
        <div class="left-section">
            <div class="info-boxes">
                <div class="info-box">
                    <h3>PEMASUKAN</h3>
                    <div class="amount">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                </div>
                <div class="info-box">
                    <h3>PENGELUARAN</h3>
                    <div class="amount">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                </div>
                <div class="info-box profit">
                    <h3>LABA BERSIH</h3>
                    <div class="amount">Rp {{ number_format($labaBersih, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="right-section">
            {{-- Ganti nama-video.mp4 dengan nama file video kamu di folder public/videos/ --}}
            <div class="illustration">
                <img src="{{ asset('images/logo2.png') }}" alt="Ilustrasi Toko Sembako" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection