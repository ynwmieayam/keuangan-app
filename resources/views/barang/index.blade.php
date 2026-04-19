@extends('layouts.app')

@section('title', 'Data Barang - Toko Sembako')

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
        font-family: 'Lato', sans-serif;
        font-size: 1em;
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
        margin-left: 10px;
    }

    /* Main Content */
    .main-content {
        display: flex;
        gap: 30px;
        margin-top: 30px;
    }

    /* Left Section */
    .left-section { flex: 0 0 350px; }
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
    .form-group { margin-bottom: 20px; }
    .form-group label {
        display: block;
        color: #4a5d4a;
        font-family: 'Lato', sans-serif;
        font-size: 1em;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #4a5d4a;
        border-radius: 5px;
        font-family: 'Lato', sans-serif;
        font-size: 1em;
    }
    .form-group input[readonly] {
        background-color: #f0f0f0;
        color: #1565c0;
        font-weight: 600;
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
    }
    .btn-simpan, .btn-edit { background-color: #a8c5a8; }
    .btn:hover { opacity: 0.8; }

    /* Right Section */
    .right-section { flex: 1; }
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
        padding-bottom: 15px;
        border-bottom: 2px solid #c5d9c5;
        margin-bottom: 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Lato', sans-serif;
        margin-top: 15px;
    }
    thead { background-color: #c5d9c5; }
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
    tbody tr { cursor: pointer; transition: background-color 0.2s; }
    tbody tr:hover    { background-color: #f5f5f5; }
    tbody tr.selected { background-color: #e8f5e8; }

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
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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
        <form action="{{ route('barang.index') }}" method="GET">
            <label>Search :</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama barang...">
            <button type="submit">Cari</button>
            @if($search)
                <a href="{{ route('barang.index') }}" style="margin-left: 10px; color: #4a5d4a; font-family: 'Lato', sans-serif; font-weight: 700;">Reset</a>
            @endif
        </form>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Left Section -->
        <div class="left-section">
            <div class="input-box">
                <h2 id="formTitle">INPUT DATA BARANG</h2>

                <form action="{{ route('barang.store') }}" method="POST" id="barangForm">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="barang_id" id="barangId">

                    <div class="form-group">
                        <label>Nama Barang :</label>
                        <input type="text" name="nama_barang" id="inputNama" value="{{ old('nama_barang') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Stok :</label>
                        <input type="number" name="stok_barang" id="inputStok" value="{{ old('stok_barang') }}" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Harga :</label>
                        <input type="number" name="harga_barang" id="inputHarga" value="{{ old('harga_barang') }}" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Modal per Satuan :</label>
                        <input type="text" id="displayModalPerSatuan" value="–" readonly>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-simpan" id="btnSimpan">Simpan Data</button>
                        <button type="button" class="btn btn-edit" id="btnEdit" onclick="resetForm()">Edit Data</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <div class="table-box">
                <h2>DATA BARANG</h2>

                @if($barang->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Stok</th>
                                <th>Harga Jual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barang as $item)
                                <tr onclick="editBarang({{ $item->id_barang }}, '{{ addslashes($item->nama_barang) }}', {{ $item->stok_barang }}, {{ $item->harga_barang }}, {{ $item->modal_per_satuan }})"
                                    data-id="{{ $item->id_barang }}">
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>{{ $item->stok_barang }} pcs</td>
                                    <td>Rp {{ number_format($item->harga_barang, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-data">
                        @if($search)
                            Barang "{{ $search }}" tidak ditemukan
                        @else
                            Belum ada data barang
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
    function editBarang(id, nama, stok, harga, modalPerSatuan) {
        document.querySelectorAll('tbody tr').forEach(row => row.classList.remove('selected'));
        document.querySelector(`tbody tr[data-id="${id}"]`).classList.add('selected');

        document.getElementById('barangId').value   = id;
        document.getElementById('inputNama').value  = nama;
        document.getElementById('inputStok').value  = stok;
        document.getElementById('inputHarga').value = harga;
        
        // Tampilkan modal per satuan
        if (modalPerSatuan > 0) {
            document.getElementById('displayModalPerSatuan').value = 'Rp ' + Number(modalPerSatuan).toLocaleString('id-ID');
        } else {
            document.getElementById('displayModalPerSatuan').value = '–';
        }

        document.getElementById('barangForm').action     = `/barang/${id}`;
        document.getElementById('formMethod').value      = 'PUT';
        document.getElementById('formTitle').textContent = 'EDIT DATA BARANG';
        document.getElementById('btnSimpan').textContent = 'Update Data';
        document.getElementById('btnEdit').textContent   = 'Batal';
    }

    function resetForm() {
        document.getElementById('barangForm').reset();
        document.getElementById('barangForm').action     = '{{ route('barang.store') }}';
        document.getElementById('formMethod').value      = 'POST';
        document.getElementById('formTitle').textContent = 'INPUT DATA BARANG';
        document.getElementById('btnSimpan').textContent = 'Simpan Data';
        document.getElementById('btnEdit').textContent   = 'Edit Data';
        document.getElementById('barangId').value        = '';
        document.getElementById('displayModalPerSatuan').value = '–';

        document.querySelectorAll('tbody tr').forEach(row => row.classList.remove('selected'));
    }
</script>
@endsection