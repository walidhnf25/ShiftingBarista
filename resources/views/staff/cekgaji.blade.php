@extends('layouts.tabler')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-gray-800">Gaji Anda</h1>
    </div>
    <p class="h6 mb-4">Mohon perhatikan gaji yang anda dapatkan, jika ada kesalahan bisa langsung dipertanyakan.</p>

    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <!-- Filter Tanggal -->
            <div class="mb-4">
                <form action="{{ route('staff.cekgaji.filter') }}" method="GET">
                    <div class="form-row align-items-center">
                        <div class="col-md-4">
                            <label for="start_date">Tanggal Mulai:</label>
                            <input 
                                type="date" 
                                id="start_date" 
                                name="start_date" 
                                class="form-control" 
                                value="{{ request('start_date', date('Y-m-01')) }}" 
                                required>
                        </div>
                        <div class="col-md-4">
                            <label for="end_date">Tanggal Selesai:</label>
                            <input 
                                type="text" 
                                id="end_date_display" 
                                class="form-control" 
                                value="{{ now()->format('d/m/Y') }}" 
                                readonly>
                        </div>
                        <div class="col-md-4 align-self-end">
                            <button type="submit" class="btn btn-primary btn-block mt-2">Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable">
                    <thead>
                        <tr>
                            <th>Outlet</th>
                            <th>Pekerjaan</th>
                            <th>Jumlah Shift</th>
                            <th>Gaji per Shift</th>
                            <th>Total Gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataGaji as $item)
                            <tr>
                                <td>{{ $item->nama_outlet }}</td>
                                <td>{{ $item->nama_pekerjaan }}</td>
                                <td>{{ $item->jumlah_shift }}</td>
                                <td>{{ number_format($item->gaji_per_shift, 2) }}</td>
                                <td>{{ number_format($item->total_gaji, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
