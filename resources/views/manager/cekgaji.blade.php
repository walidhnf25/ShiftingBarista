@extends('layouts.tabler')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cek Gaji Staff - {{ $selectedOutlet['outlet_name'] ?? 'Unknown' }} </h1>
    </div>

    <div class="row">
        <div class="col mb-3">
            <form action="{{ route('manager.cekgaji.filter', ['id' => $selectedOutlet['id'] ?? 0]) }}" method="GET">
                @csrf
                <div class="btn-group">
                    <select name="id_periode" class="form-control" onchange="this.form.submit()">
                        <option value="">Pilih Periode</option>
                        @foreach ($periode_gaji as $periode)
                            <option value="{{ $periode->id }}" @if (request('id_periode') == $periode->id) selected @endif>
                                {{ $periode->nama_periode_gaji }} |
                                {{ \Carbon\Carbon::parse($periode->tgl_mulai)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($periode->tgl_akhir)->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Container untuk Search dan Tabel -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Data Gaji Pegawai</h6>
        </div>
        <div class="card-body">
            @if (request('id_periode'))
                <!-- Form Pencarian Nama Pegawai -->
                <div class="row">
                    <div class="col-md-6">
                        <div class=" mb-3">
                            <form action="{{ route('manager.cekgaji.search', ['id' => $selectedOutlet['id'] ?? 0]) }}" method="GET">
                                <input type="hidden" name="id_periode" value="{{ request('id_periode') }}">
                                <div class="form-row align-items-center">
                                    <div class="col-md-12 col-lg-6">
                                        <label for="search_query">Cari Pegawai:</label>
                                        <div class="input-group">
                                            <input type="text" id="search_query" name="search_query" class="form-control"
                                                value="{{ request('search_query') }}" placeholder="Nama Pegawai">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-search" aria-hidden="true"></i>
                                                </button>
                                                @if (request('search_query'))
                                                    <button type="button" class="btn btn-danger" onclick="resetSearch()">
                                                        Reset
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end align-items-center">
                        <!-- Tombol untuk Cetak PDF -->
                        <a href="{{ route('manager.cekgaji.cetakpdf', [
                            'id' => $selectedOutlet['id'] ?? 0,
                            'id_periode' => request('id_periode'),
                        ]) }}"
                            class="btn btn-danger"
                            target="_blank">
                            <i class="fa fa-file-pdf mr-2" aria-hidden="true"></i> Cetak PDF
                        </a>
                    </div>
                </div>


                <!-- Tabel Data Gaji -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable">
                        <thead>
                            <tr>
                                <th>Nama Pegawai</th>
                                <th>Pekerjaan</th>
                                <th>Jumlah Shift</th>
                                <th>Total Gaji</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($dataGaji->count() > 0)
                                @foreach ($dataGaji as $gaji)
                                    @if ($gaji)
                                        <tr data-toggle="collapse" data-target="#accordion-{{ $loop->index }}"
                                            class="clickable" style="cursor: pointer">
                                            <td>{{ $gaji->nama_pegawai }}</td>
                                            <td>{{ $gaji->nama_pekerjaan }}</td>
                                            <td>
                                                <span style="cursor: pointer" data-toggle="tooltip"
                                                    title="{{ $gaji->tanggal_shift }}" class="badge badge-primary">
                                                    {{ $gaji->jumlah_shift }} Hari
                                                </span>
                                            </td>
                                            <td> Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }} </td>
                                        </tr>
                                        <tr id="accordion-{{ $loop->index }}" class="collapse">
                                            <td colspan="4">
                                                <h5 class="mt-2 ml-3">Detail Data Gaji {{ $gaji->nama_pegawai }} </h5>
                                                <div class="p-3">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Tanggal</th>
                                                                <th>Pendapatan Outlet</th>
                                                                <th>Fee</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if ($gaji->detail_shifts->count() > 0)
                                                                @foreach ($gaji->detail_shifts as $shift)
                                                                    <tr>
                                                                        <td>{{ \Carbon\Carbon::parse($shift->tanggal)->format('d/m/Y') }}
                                                                        </td>
                                                                        <td>Rp
                                                                            {{ number_format($shift->revenue, 0, ',', '.') }}
                                                                        </td>
                                                                        <td>Rp
                                                                            {{ number_format($shift->fee, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="3" class="text-center"> Tidak ada data
                                                                        shift. </td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">Data tidak ditemukan</td>
                                </tr>
                            @endif
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
            @else
                <div class="text-center text-muted">Silakan Pilih Periode Dahulu.</div>
            @endif
        </div>
    </div>

@endsection


@push('myscript')
    <script>
        function resetSearch() {
            const url = new URL(window.location.href);
            const id_periode = url.searchParams.get('id_periode');
            // Buat URL baru tanpa parameter search_query
            const newUrl = `${url.origin}${url.pathname}?id_periode=${id_periode}&search_query=${""}`;
            window.location.href = newUrl;
        }


        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip(); // Inisialisasi semua tooltip
        });
    </script>
@endpush
