@extends('layouts.app')

@section('title', 'Laporan Harian - Toko Sembako')

@section('additional-styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<style>
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
    .search-container input[type="date"] {
        padding: 10px 15px;
        border: 2px solid #4a5d4a;
        border-radius: 8px;
        font-family: 'Lato', sans-serif;
        font-size: 1em;
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

    .table-container {
        background-color: white;
        border: 2px solid #4a5d4a;
        border-radius: 15px;
        padding: 30px;
        margin-top: 30px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Lato', sans-serif;
    }
    thead {
        background-color: #c5d9c5;
    }
    th {
        padding: 15px;
        text-align: center;
        color: #4a5d4a;
        font-family: 'Lato', sans-serif;
        font-weight: 700;
        letter-spacing: 1px;
        font-size: 0.95em;
        border: 1px solid #4a5d4a;
    }
    td {
        padding: 15px;
        text-align: center;
        border: 1px solid #ddd;
        color: #4a5d4a;
        font-family: 'Lato', sans-serif;
        font-size: 0.95em;
    }
    tbody tr:hover {
        background-color: #f5f5f5;
    }
    .no-data {
        text-align: center;
        padding: 40px;
        color: #888;
        font-family: 'Lato', sans-serif;
        font-style: italic;
    }
    .laba-positif {
        color: #1a8a1a;
        font-family: 'Playfair Display', serif;
        font-weight: 600;
    }
    .laba-negatif {
        color: #d32f2f;
        font-family: 'Playfair Display', serif;
        font-weight: 600;
    }

    /* Modal Popup */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }
    .modal.active {
        display: flex;
    }
    .modal-content {
        background-color: white;
        padding: 30px;
        border-radius: 15px;
        border: 3px solid #4a5d4a;
        width: 90%;
        max-width: 800px;
        max-height: 80vh;
        overflow-y: auto;
        font-family: 'Lato', sans-serif;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #c5d9c5;
    }
    .modal-header h2 {
        color: #4a5d4a;
        font-family: 'Playfair Display', serif;
        font-size: 1.4em;
        font-weight: 600;
        letter-spacing: 1px;
    }
    .close-btn {
        background-color: #f5a5a5;
        border: 2px solid #4a5d4a;
        border-radius: 8px;
        padding: 8px 20px;
        cursor: pointer;
        font-family: 'Lato', sans-serif;
        font-weight: 700;
        letter-spacing: 1px;
        color: #4a5d4a;
    }
    .detail-section {
        margin-bottom: 25px;
    }
    .detail-section h3 {
        color: #4a5d4a;
        font-family: 'Lato', sans-serif;
        font-size: 1em;
        font-weight: 700;
        letter-spacing: 2px;
        margin-bottom: 10px;
        padding: 10px;
        background-color: #c5d9c5;
        border-radius: 5px;
    }
    .detail-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        font-family: 'Lato', sans-serif;
    }
    .detail-table th {
        background-color: #e8f5e8;
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
        color: #4a5d4a;
        font-weight: 700;
        letter-spacing: 1px;
        font-size: 0.9em;
    }
    .detail-table td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
        color: #4a5d4a;
        font-size: 0.9em;
    }
    .clickable-row {
        cursor: pointer;
    }
    .clickable-row:hover {
        background-color: #e8f5e8 !important;
    }
</style>
@endsection

@section('content')
<div class="container">
    <!-- Search Bar -->
    <div class="search-container">
        <form action="{{ route('laporan.harian') }}" method="GET" style="display: flex; align-items: center; gap: 15px;">
            <label>SEARCH :</label>
            <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" required>
            <span style="color: #4a5d4a; font-family: 'Lato', sans-serif; font-weight: 700;">s/d</span>
            <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" required>
            <button type="submit">Cari</button>
            <a href="{{ route('laporan.harian') }}" style="color: #4a5d4a; text-decoration: none; font-family: 'Lato', sans-serif; font-weight: 700; letter-spacing: 1px;">Reset</a>
        </form>
    </div>

    <!-- Table -->
    <div class="table-container">
        @if($laporan->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">NO</th>
                        <th style="width: 40%;">Tanggal</th>
                        <th style="width: 50%;">Laba kotor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laporan as $index => $item)
                        <tr class="clickable-row" onclick="showDetail('{{ $item['tanggal'] }}')">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['tanggal_format'] }}</td>
                            <td class="{{ $item['laba_bersih'] >= 0 ? 'laba-positif' : 'laba-negatif' }}">
                                Rp {{ number_format($item['laba_bersih'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Tidak ada data untuk periode {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}
            </div>
        @endif
    </div>

    <!-- Modal Popup -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Detail Transaksi</h2>
                <button class="close-btn" onclick="closeModal()">Tutup</button>
            </div>

            <div class="detail-section">
                <h3>📥 PEMASUKAN</h3>
                <table class="detail-table" id="pemasukanTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="pemasukanBody"></tbody>
                </table>
                <p style="text-align: right; font-family: 'Lato', sans-serif; font-weight: 700; color: #1a8a1a; margin-top: 10px; letter-spacing: 1px;">
                    Total Pemasukan: <span id="totalPemasukan" style="font-family: 'Playfair Display', serif; font-size: 1.1em;">Rp 0</span>
                </p>
            </div>

            <div class="detail-section">
                <h3>📤 PENGELUARAN</h3>
                <table class="detail-table" id="pengeluaranTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="pengeluaranBody"></tbody>
                </table>
                <p style="text-align: right; font-family: 'Lato', sans-serif; font-weight: 700; color: #d32f2f; margin-top: 10px; letter-spacing: 1px;">
                    Total Pengeluaran: <span id="totalPengeluaran" style="font-family: 'Playfair Display', serif; font-size: 1.1em;">Rp 0</span>
                </p>
            </div>

            <div style="margin-top: 20px; padding: 15px; background-color: #e8f5e8; border-radius: 8px; text-align: center;">
                <strong style="color: #4a5d4a; font-family: 'Lato', sans-serif; font-size: 1em; letter-spacing: 2px;">
                    LABA BERSIH: <span id="labaBersihModal" style="font-family: 'Playfair Display', serif; font-size: 1.3em;">Rp 0</span>
                </strong>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const detailData = @json($detailTransaksi ?? []);

    function showDetail(tanggal) {
        const modal = document.getElementById('detailModal');
        const modalTitle = document.getElementById('modalTitle');
        const pemasukanBody = document.getElementById('pemasukanBody');
        const pengeluaranBody = document.getElementById('pengeluaranBody');

        const tanggalObj = new Date(tanggal);
        const options = { day: '2-digit', month: 'long', year: 'numeric' };
        const tanggalFormat = tanggalObj.toLocaleDateString('id-ID', options);

        modalTitle.textContent = `Detail Transaksi - ${tanggalFormat}`;

        const detail = detailData[tanggal] || { pemasukan: [], pengeluaran: [] };

        pemasukanBody.innerHTML = '';
        let totalPemasukan = 0;
        if (detail.pemasukan.length > 0) {
            detail.pemasukan.forEach((item, index) => {
                totalPemasukan += parseFloat(item.total);
                pemasukanBody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.nama_barang}</td>
                        <td>${item.jumlah}</td>
                        <td>Rp ${parseInt(item.total).toLocaleString('id-ID')}</td>
                    </tr>
                `;
            });
        } else {
            pemasukanBody.innerHTML = '<tr><td colspan="4" style="text-align: center; font-style: italic; color: #888;">Tidak ada pemasukan</td></tr>';
        }

        pengeluaranBody.innerHTML = '';
        let totalPengeluaran = 0;
        if (detail.pengeluaran.length > 0) {
            detail.pengeluaran.forEach((item, index) => {
                totalPengeluaran += parseFloat(item.total);
                pengeluaranBody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.nama_barang}</td>
                        <td>${item.jumlah}</td>
                        <td>Rp ${parseInt(item.total).toLocaleString('id-ID')}</td>
                    </tr>
                `;
            });
        } else {
            pengeluaranBody.innerHTML = '<tr><td colspan="4" style="text-align: center; font-style: italic; color: #888;">Tidak ada pengeluaran</td></tr>';
        }

        document.getElementById('totalPemasukan').textContent = `Rp ${totalPemasukan.toLocaleString('id-ID')}`;
        document.getElementById('totalPengeluaran').textContent = `Rp ${totalPengeluaran.toLocaleString('id-ID')}`;

        const labaBersih = totalPemasukan - totalPengeluaran;
        const labaBersihSpan = document.getElementById('labaBersihModal');
        labaBersihSpan.textContent = `Rp ${labaBersih.toLocaleString('id-ID')}`;
        labaBersihSpan.style.color = labaBersih >= 0 ? '#1a8a1a' : '#d32f2f';

        modal.classList.add('active');
    }

    function closeModal() {
        document.getElementById('detailModal').classList.remove('active');
    }

    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endsection