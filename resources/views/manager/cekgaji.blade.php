@extends('layouts.tabler')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Cek Gaji Pegawai</h1>
</div>

<!-- Filter Tanggal -->
<div class="mb-4">
    <form method="GET" >
        <div class="form-row align-items-center">
            <div class="col-md-4">
                <label for="start_date">Tanggal Mulai:</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date', date('Y-m-01')) }}" >
            </div>
            <div class="col-md-4">
                <label for="end_date">Tanggal Selesai:</label>
                <input type="text" id="end_date" name="end_date" class="form-control" value="{{ now()->format('Y-m-d H:i:s') }}" readonly>
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary btn-block mt-2">Filter</button>
            </div>
        </div>
    </form>
</div>

<!-- Tabel Data Gaji -->
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTable">
        <thead>
            <tr>
                <th>Nama Pegawai</th>
                <th>Jumlah Shift</th>
                <th>Total Gaji</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dummy data -->
            <tr>
                <td>John Doe</td>
                <td>17</td>
                <td>Rp 8,000,000</td>
            </tr>
            <tr>
                <td>Jane Smith</td>
                <td>20</td>
                <td>Rp 5,500,000</td>
            </tr>
            <tr>
                <td>Michael Johnson</td>
                <td>18</td>
                <td>Rp 5,500,000</td>
            </tr>
        </tbody>
    </table>
</div>

@endsection
