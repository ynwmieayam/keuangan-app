@extends('layouts.app')

@section('title', 'Laporan Bulanan - Toko Sembako')

@section('additional-styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<style>
    /* ── Search bar ─────────────────────────────────── */
    .search-container {
        margin-bottom: 40px;
        padding-bottom: 12px;
        border-bottom: 2px solid #4a5d4a;
        display: flex;
        align-items: center;
        gap: 15px;
        font-family: 'Lato', sans-serif;
    }
    .search-container label {
        color: #4a5d4a;
        font-family: 'Lato', sans-serif;
        font-size: 1em;
        font-weight: 700;
        letter-spacing: 1px;
    }
    .search-container select {
        padding: 10px 20px;
        border: 2px solid #4a5d4a;
        border-radius: 8px;
        font-family: 'Lato', sans-serif;
        font-size: 1em;
        background-color: white;
        cursor: pointer;
        min-width: 150px;
    }
    .search-container button {
        padding: 10px 30px;
        background-color: #a8c5a8;
        border: 2px solid #4a5d4a;
        border-radius: 8px;
        cursor: pointer;
        font-family: 'Lato', sans-serif;
        font-size: 1em;
        font-weight: 700;
        letter-spacing: 1px;
        color: #4a5d4a;
    }



    /* ── Table ──────────────────────────────────────── */
    .table-container {
        background-color: white;
        border: 2px solid #4a5d4a;
        border-radius: 15px;
        padding: 30px;
        min-height: 200px;
        margin-top: 30px;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #888;
        font-family: 'Lato', sans-serif;
        font-size: 1em;
        font-style: italic;
    }
    table { width: 100%; border-collapse: collapse; font-family: 'Lato', sans-serif; }
    thead { background-color: #c5d9c5; }
    th {
        padding: 15px;
        text-align: center;
        color: #4a5d4a;
        font-family: 'Lato', sans-serif;
        font-weight: 700;
        letter-spacing: 1px;
        border: 1px solid #4a5d4a;
        font-size: 0.95em;
    }
    td {
        padding: 15px;
        text-align: center;
        border: 1px solid #ddd;
        color: #4a5d4a;
        font-family: 'Lato', sans-serif;
        font-size: 0.95em;
    }
    tbody tr { cursor: pointer; transition: background 0.15s; }
    tbody tr:hover { background-color: #eaf4ea; }

    .laba-positif { color: #1a8a1a; font-family: 'Playfair Display', serif; font-weight: 600; }
    .laba-negatif { color: #d32f2f; font-family: 'Playfair Display', serif; font-weight: 600; }
    .laba-zero    { color: #888; font-family: 'Playfair Display', serif; font-style: italic; }



    /* ── Modal umum ─────────────────────────────────── */
    .modal-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active { display: flex; }

    .modal-box {
        background: white;
        border-radius: 15px;
        padding: 30px;
        width: 90%;
        max-width: 700px;
        max-height: 85vh;
        overflow-y: auto;
        position: relative;
        box-shadow: 0 10px 40px rgba(0,0,0,0.25);
        font-family: 'Lato', sans-serif;
    }

    .modal-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.3em;
        font-weight: 600;
        letter-spacing: 1px;
        color: #4a5d4a;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #c5d9c5;
    }

    .modal-close {
        position: absolute;
        top: 15px; right: 18px;
        background: none; border: none;
        font-size: 1.6em; cursor: pointer;
        color: #4a5d4a; line-height: 1;
    }
    .modal-close:hover { color: #d32f2f; }

    /* ── Daftar hari dalam modal bulan ─────────────── */
    .hari-list { display: flex; flex-direction: column; gap: 8px; }

    .hari-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border: 1.5px solid #c5d9c5;
        border-radius: 9px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .hari-item:hover { background: #eaf4ea; }

    .hari-tanggal { font-weight: 700; color: #4a5d4a; letter-spacing: 0.5px; }
    .hari-laba { font-family: 'Playfair Display', serif; font-weight: 600; font-size: 1em; }

    /* ── Detail hari ────────────────────────────────── */
    .back-btn {
        display: inline-flex; align-items: center; gap: 6px;
        background: none; border: none;
        color: #4a5d4a; font-family: 'Lato', sans-serif;
        font-size: 0.95em; font-weight: 700;
        cursor: pointer; margin-bottom: 18px;
        padding: 0;
    }
    .back-btn:hover { text-decoration: underline; }

    .summary-cards {
        display: grid; grid-template-columns: 1fr 1fr 1fr;
        gap: 12px; margin-bottom: 22px;
    }
    .summary-card {
        border-radius: 10px; padding: 14px 16px; text-align: center;
    }
    .summary-card.pemasukan { background: #e8f5e9; border: 1.5px solid #81c784; }
    .summary-card.pengeluaran { background: #fce4ec; border: 1.5px solid #e57373; }
    .summary-card.laba { background: #e3f2fd; border: 1.5px solid #64b5f6; }
    .summary-card .card-label {
        font-family: 'Lato', sans-serif;
        font-size: 0.75em;
        font-weight: 700;
        letter-spacing: 1px;
        color: #666;
        margin-bottom: 6px;
        text-transform: uppercase;
    }
    .summary-card .card-value {
        font-family: 'Playfair Display', serif;
        font-size: 1.05em;
        font-weight: 600;
    }
    .summary-card.pemasukan .card-value { color: #2e7d32; }
    .summary-card.pengeluaran .card-value { color: #c62828; }
    .summary-card.laba .card-value { color: #1565c0; }

    .detail-section { margin-bottom: 18px; }
    .detail-section h4 {
        font-family: 'Lato', sans-serif;
        font-size: 0.9em;
        font-weight: 700;
        letter-spacing: 2px;
        color: #4a5d4a;
        margin-bottom: 10px;
        padding-left: 10px;
        border-left: 4px solid #4a5d4a;
        text-transform: uppercase;
    }
    .detail-table { width: 100%; border-collapse: collapse; font-size: 0.92em; font-family: 'Lato', sans-serif; }
    .detail-table th {
        background: #c5d9c5; padding: 9px 12px;
        text-align: center; color: #4a5d4a;
        border: 1px solid #b0c9b0;
        font-family: 'Lato', sans-serif;
        font-weight: 700;
        letter-spacing: 1px;
        font-size: 0.9em;
    }
    .detail-table td {
        padding: 9px 12px; border: 1px solid #ddd;
        text-align: center; color: #4a5d4a;
        font-family: 'Lato', sans-serif;
    }
    .detail-table tbody tr:hover { background: #f5f5f5; }
    .no-data { text-align: center; color: #aaa; font-style: italic; padding: 12px; font-family: 'Lato', sans-serif; }
</style>
@endsection

@section('content')
<div class="container">

    {{-- ── Search ─────────────────────────────────────────── --}}
    <div class="search-container">
        <form action="{{ route('laporan.bulanan') }}" method="GET"
              style="display: flex; align-items: center; gap: 15px;">
            <label>SEARCH :</label>
            <select name="tahun" required>
                @foreach($tahunList as $t)
                    <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
            <button type="submit">Cari</button>
            <a href="{{ route('laporan.bulanan') }}"
               style="color: #4a5d4a; text-decoration: none; font-family: 'Lato', sans-serif; font-weight: 700; letter-spacing: 1px;">Reset</a>
        </form>
    </div>



    {{-- ── Tabel bulan ─────────────────────────────────────── --}}
    <div class="table-container">
        @if($laporan->isEmpty())
            <div class="empty-state">
                Tidak ada data laporan untuk tahun {{ $tahun }}.
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width:8%;">NO</th>
                        <th style="width:32%;">Bulan/Tahun</th>
                        <th style="width:30%;">Laba Kotor</th>
                        <th style="width:30%;">Laba Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan as $index => $item)
                        <tr onclick="bukaModalBulan({{ $item['bulan'] }})"
                            title="Klik untuk lihat detail {{ $item['nama_bulan'] }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['bulan_tahun'] }}</td>
                            <td class="@if($item['laba_kotor'] > 0) laba-positif
                                        @elseif($item['laba_kotor'] < 0) laba-negatif
                                        @else laba-zero @endif">
                                Rp {{ number_format($item['laba_kotor'], 0, ',', '.') }}
                            </td>
                            <td class="@if($item['laba_bersih'] > 0) laba-positif
                                        @elseif($item['laba_bersih'] < 0) laba-negatif
                                        @else laba-zero @endif">
                                Rp {{ number_format($item['laba_bersih'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     MODAL 1 – Daftar Hari dalam Bulan
════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalBulan">
    <div class="modal-box">
        <button class="modal-close" onclick="tutupSemua()">✕</button>
        <div class="modal-title" id="modalBulanJudul">Detail Bulan</div>
        <div class="hari-list" id="hariList"></div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     MODAL 2 – Detail Transaksi Per Hari
════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalHari">
    <div class="modal-box">
        <button class="modal-close" onclick="tutupSemua()">✕</button>
        <button class="back-btn" onclick="kembaliKeBulan()">← Kembali</button>
        <div class="modal-title" id="modalHariJudul">Detail Hari</div>

        <div class="summary-cards">
            <div class="summary-card pemasukan">
                <div class="card-label">Total Pemasukan</div>
                <div class="card-value" id="detailTotalPemasukan">–</div>
            </div>
            <div class="summary-card pengeluaran">
                <div class="card-label">Total Pengeluaran</div>
                <div class="card-value" id="detailTotalPengeluaran">–</div>
            </div>
            <div class="summary-card laba">
                <div class="card-label">Laba Harian</div>
                <div class="card-value" id="detailLabaBersih">–</div>
            </div>
        </div>

        <div class="detail-section">
            <h4>Pemasukan (Penjualan)</h4>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jumlah Terjual</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="tabelPemasukan"></tbody>
            </table>
        </div>

        <div class="detail-section">
            <h4>Pengeluaran (Pembelian Stok)</h4>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jumlah Dibeli</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="tabelPengeluaran"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const detailHarian = @json($detailHarian);
const laporanBulan = @json($laporan->keyBy('bulan'));

function rp(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

function bukaModalBulan(bulan) {
    bulanAktif = bulan;
    const info = laporanBulan[bulan];
    document.getElementById('modalBulanJudul').textContent = 'Detail ' + info.bulan_tahun;

    const hari = detailHarian[bulan] || [];
    const list = document.getElementById('hariList');
    list.innerHTML = '';

    if (hari.length === 0) {
        list.innerHTML = '<p class="no-data">Tidak ada data harian untuk bulan ini.</p>';
    } else {
        hari.forEach(h => {
            const laba = h.laba_bersih;
            const kelasLaba = laba > 0 ? '#1a8a1a' : laba < 0 ? '#d32f2f' : '#888';
            const div = document.createElement('div');
            div.className = 'hari-item';
            div.innerHTML = `
                <span class="hari-tanggal">${h.tanggal_format}</span>
                <span class="hari-laba" style="color:${kelasLaba}">${rp(laba)}</span>
            `;
            div.onclick = () => bukaModalHari(h);
            list.appendChild(div);
        });
    }

    document.getElementById('modalBulan').classList.add('active');
}

function bukaModalHari(h) {
    document.getElementById('modalBulan').classList.remove('active');

    document.getElementById('modalHariJudul').textContent = h.tanggal_format;
    document.getElementById('detailTotalPemasukan').textContent  = rp(h.total_pemasukan);
    document.getElementById('detailTotalPengeluaran').textContent = rp(h.total_pengeluaran);

    const laba = h.laba_bersih;
    const elLaba = document.getElementById('detailLabaBersih');
    elLaba.textContent = rp(laba);
    elLaba.style.color = laba > 0 ? '#1565c0' : laba < 0 ? '#c62828' : '#555';

    const tbP = document.getElementById('tabelPemasukan');
    tbP.innerHTML = '';
    if (h.pemasukan.length === 0) {
        tbP.innerHTML = '<tr><td colspan="3" class="no-data">Tidak ada pemasukan</td></tr>';
    } else {
        h.pemasukan.forEach(p => {
            tbP.innerHTML += `<tr>
                <td>${p.nama_barang}</td>
                <td>${p.jumlah}</td>
                <td>${rp(p.total)}</td>
            </tr>`;
        });
    }

    const tbK = document.getElementById('tabelPengeluaran');
    tbK.innerHTML = '';
    if (h.pengeluaran.length === 0) {
        tbK.innerHTML = '<tr><td colspan="3" class="no-data">Tidak ada pengeluaran</td></tr>';
    } else {
        h.pengeluaran.forEach(k => {
            tbK.innerHTML += `<tr>
                <td>${k.nama_barang}</td>
                <td>${k.jumlah}</td>
                <td>${rp(k.total)}</td>
            </tr>`;
        });
    }

    document.getElementById('modalHari').classList.add('active');
}

function kembaliKeBulan() {
    document.getElementById('modalHari').classList.remove('active');
    if (bulanAktif !== null) bukaModalBulan(bulanAktif);
}

function tutupSemua() {
    document.getElementById('modalBulan').classList.remove('active');
    document.getElementById('modalHari').classList.remove('active');
    bulanAktif = null;
}

document.getElementById('modalBulan').addEventListener('click', function(e) {
    if (e.target === this) tutupSemua();
});
document.getElementById('modalHari').addEventListener('click', function(e) {
    if (e.target === this) tutupSemua();
});
</script>
@endsection