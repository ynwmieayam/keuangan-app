@extends('layouts.app')

@section('title', 'Pengeluaran - Toko Sembako')

@section('additional-styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<style>
    /* Search Bar */
    .search-container {
        margin-bottom: 40px;
        padding-bottom: 12px;
        border-bottom: 2px solid #4a5d4a;
        font-family: 'Lato', sans-serif;
    }
    .search-container label {
        color: #4a5d4a;
        font-size: 1.1em;
        font-weight: 700;
        letter-spacing: 1px;
        margin-right: 10px;
    }
    .search-container input[type="text"] {
        width: 700px;
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
        margin-left: 15px;
    }

    /* Main Content */
    .main-content {
        display: flex;
        gap: 30px;
        margin-top: 30px;
    }

    /* Left Section - Input Form */
    .left-section {
        flex: 0 0 350px;
    }
    .input-box {
        background-color: white;
        border: 2px solid #4a5d4a;
        border-radius: 15px;
        padding: 30px;
    }
    .input-box h2 {
        color: #4a5d4a;
        text-align: center;
        font-family: 'Playfair Display', serif;
        font-size: 1.3em;
        font-weight: 600;
        letter-spacing: 2px;
        margin-bottom: 30px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        color: #4a5d4a;
        font-family: 'Lato', sans-serif;
        font-size: 1em;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #4a5d4a;
        border-radius: 5px;
        font-family: 'Lato', sans-serif;
        font-size: 1em;
    }
    .form-group input[readonly] {
        background-color: #f0f0f0;
        cursor: not-allowed;
    }
    .button-group {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 30px;
    }
    .btn {
        padding: 12px;
        border: 2px solid #4a5d4a;
        border-radius: 8px;
        font-family: 'Lato', sans-serif;
        font-size: 1em;
        font-weight: 700;
        letter-spacing: 1px;
        cursor: pointer;
        color: #4a5d4a;
        background-color: #a8c5a8;
    }
    .btn:hover {
        opacity: 0.8;
    }

    /* Right Section - Table */
    .right-section {
        flex: 1;
    }
    .table-box {
        background-color: white;
        border: 2px solid #4a5d4a;
        border-radius: 15px;
        padding: 30px;
        min-height: 400px;
    }
    .table-box h2 {
        color: #4a5d4a;
        text-align: center;
        font-family: 'Playfair Display', serif;
        font-size: 1.3em;
        font-weight: 600;
        letter-spacing: 2px;
        margin-bottom: 20px;
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
        padding: 12px;
        text-align: center;
        color: #4a5d4a;
        font-family: 'Lato', sans-serif;
        font-weight: 700;
        letter-spacing: 1px;
        font-size: 0.95em;
        border: 1px solid #4a5d4a;
    }
    td {
        padding: 12px;
        text-align: center;
        border: 1px solid #ddd;
        color: #4a5d4a;
        font-family: 'Lato', sans-serif;
        font-size: 0.95em;
    }
    tbody tr {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    tbody tr:hover {
        background-color: #f5f5f5;
    }
    tbody tr.selected {
        background-color: #e8f5e8;
    }
    .no-data {
        text-align: center;
        padding: 40px;
        color: #888;
        font-family: 'Lato', sans-serif;
        font-style: italic;
    }
</style>
@endsection

@section('content')
<div class="container">
    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="search-container">
        <form action="{{ route('pengeluaran.index') }}" method="GET">
            <label>search:</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama barang...">
            <button type="submit">Cari</button>
            @if($search)
                <a href="{{ route('pengeluaran.index') }}" style="margin-left: 10px; color: #4a5d4a; font-family: 'Lato', sans-serif;">Reset</a>
            @endif
        </form>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Left Section - Input Form -->
        <div class="left-section">
            <div class="input-box">
                <h2 id="formTitle">INPUT PENGELUARAN</h2>

                <form action="{{ route('pengeluaran.store') }}" method="POST" id="pengeluaranForm">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="pengeluaran_id" id="pengeluaranId">

                    <div class="form-group">
                        <label>Tanggal :</label>
                        <input type="text" value="{{ date('d-m-Y') }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Nama Barang :</label>
                        <select name="id_barang" id="selectBarang" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangList as $barang)
                                <option value="{{ $barang->id_barang }}">
                                    {{ $barang->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jumlah :</label>
                        <input type="number" name="jumlah_barang_dibeli" id="inputJumlah" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Total :</label>
                        <input type="number" name="total_pengeluaran" id="inputTotal" min="0" step="0.01" required placeholder="Masukkan total pengeluaran">
                    </div>

                    <div class="form-group">
                        <label>Modal per pcs :</label>
                        <input type="text" id="displayModalPerPcs" value="Rp 0" readonly>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn" id="btnSimpan">Simpan Data</button>
                        <button type="button" class="btn" id="btnEdit" onclick="resetForm()">Edit Data</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Section - Table -->
        <div class="right-section">
            <div class="table-box">
                <h2>RIWAYAT TRANSAKSI KEUANGAN</h2>

                @if($pengeluaran->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Modal per Satuan</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengeluaran as $item)
                                @php
                                    $modalPerSatuan = $item->jumlah_barang_dibeli > 0 
                                        ? $item->total_pengeluaran / $item->jumlah_barang_dibeli 
                                        : 0;
                                @endphp
                                <tr onclick="editPengeluaran({{ $item->id_pengeluaran }}, {{ $item->id_barang }}, {{ $item->jumlah_barang_dibeli }}, {{ $item->total_pengeluaran }})" data-id="{{ $item->id_pengeluaran }}">
                                    <td>{{ $item->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                                    <td>{{ $item->jumlah_barang_dibeli }} pcs</td>
                                    <td>Rp {{ number_format($modalPerSatuan, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->total_pengeluaran, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-data">
                        @if($search)
                            Pengeluaran "{{ $search }}" tidak ditemukan
                        @else
                            Belum ada data pengeluaran
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateModalPerPcs() {
        const jumlah = parseInt(document.getElementById('inputJumlah').value) || 0;
        const total = parseFloat(document.getElementById('inputTotal').value) || 0;
        
        let modalPerPcs = 0;
        if (jumlah > 0) {
            modalPerPcs = total / jumlah;
        }

        document.getElementById('displayModalPerPcs').value = 'Rp ' + modalPerPcs.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    document.getElementById('inputJumlah').addEventListener('input', updateModalPerPcs);
    document.getElementById('inputTotal').addEventListener('input', updateModalPerPcs);

    function editPengeluaran(id, idBarang, jumlah, total) {
        document.querySelectorAll('tbody tr').forEach(row => row.classList.remove('selected'));
        document.querySelector(`tbody tr[data-id="${id}"]`).classList.add('selected');

        document.getElementById('pengeluaranId').value = id;
        document.getElementById('selectBarang').value = idBarang;
        document.getElementById('inputJumlah').value = jumlah;
        document.getElementById('inputTotal').value = total;

        updateModalPerPcs();

        document.getElementById('pengeluaranForm').action = `/pengeluaran/${id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('formTitle').textContent = 'EDIT PENGELUARAN';
        document.getElementById('btnSimpan').textContent = 'Update Data';
        document.getElementById('btnEdit').textContent = 'Batal';
    }

    function resetForm() {
        document.getElementById('pengeluaranForm').reset();
        document.getElementById('pengeluaranForm').action = '{{ route('pengeluaran.store') }}';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('formTitle').textContent = 'INPUT PENGELUARAN';
        document.getElementById('btnSimpan').textContent = 'Simpan Data';
        document.getElementById('btnEdit').textContent = 'Edit Data';
        document.getElementById('pengeluaranId').value = '';
        document.getElementById('displayModalPerPcs').value = 'Rp 0';

        document.querySelectorAll('tbody tr').forEach(row => row.classList.remove('selected'));
    }
</script>
@endsection