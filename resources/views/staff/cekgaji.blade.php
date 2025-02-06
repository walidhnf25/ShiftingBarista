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

            <!-- Filter Periode -->
            <div class="mb-4">
                <form action="{{ route('staff.cekgaji.filter') }}" method="GET">
                    <div class="btn-group">
                        <select name="id_periode" class="form-control" onchange="this.form.submit()">
                            <option value="">Pilih Periode</option>
                            @foreach ($periode_gaji as $periode)
                                <option value="{{ $periode->id }}" 
                                    @if (isset($id_periode) && $id_periode == $periode->id) 
                                        selected 
                                    @elseif (!isset($id_periode) && $currentPeriod && $currentPeriod->id == $periode->id) 
                                        selected 
                                    @endif>
                                    {{ $periode->nama_periode_gaji }} |
                                    {{ \Carbon\Carbon::parse($periode->tgl_mulai)->format('d/m/Y') }} -
                                    {{ \Carbon\Carbon::parse($periode->tgl_akhir)->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
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
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-center font-weight-bold">Total Keseluruhan Gaji</td>
                            <td class="font-weight-bold text-danger">
                                Rp {{ number_format($dataGaji->sum('total_gaji'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection