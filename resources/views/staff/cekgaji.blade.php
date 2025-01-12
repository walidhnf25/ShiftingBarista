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

            @if (session('warning'))
                <div class="alert alert-warning">
                    {{ session('warning') }}
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
                            <input type="date" id="start_date" name="start_date" class="form-control"
                                value="{{ $startDate }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="end_date">Tanggal Selesai:</label>
                            <input type="date" id="end_date" name="end_date" class="form-control"
                                value="{{ $endDate }}" required>
                        </div>
                        <div class="col-md-4 align-self-end">
                            <button type="submit" class="btn btn-primary btn-block mt-2">Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>Outlet</th>
                            <th>Pekerjaan</th>
                            <th>Jumlah Shift</th>
                            <th>Total Gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataGaji as $item)
                            <tr data-toggle="collapse" data-target="#accordion-{{ $loop->index }}" class="clickable"
                                style="cursor: pointer">
                                <td>{{ $item->nama_outlet }}</td>
                                <td>{{ $item->nama_pekerjaan }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ $item->jumlah_shift }} Hari</span>
                                </td>
                                <td>Rp {{ number_format($item->total_gaji, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="p-0 border-0">
                                    <div class="collapse" id="accordion-{{ $loop->index }}">
                                        <div class="card card-body m-2">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>Pendapatan Outlet</th>
                                                        <th>Fee</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($item->detail_shifts as $shift)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($shift->tanggal)->format('d/m/Y') }}</td>
                                                            <td>Rp {{ number_format($shift->revenue, 0, ',', '.') }}</td>
                                                            <td>Rp {{ number_format($shift->fee, 0, ',', '.') }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center">Tidak ada data shift.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Data tidak ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection