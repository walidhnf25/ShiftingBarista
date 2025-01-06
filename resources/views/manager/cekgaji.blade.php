@extends('layouts.tabler')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Cek Gaji Staff</h1>
</div>



<!-- Form Filter Tanggal -->
<div class="mb-4">
    <form action="{{ route('manager.cekgaji.filter', ['id' => $selectedOutlet['id']]) }}" method="GET">
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
                <input type="hidden" name="search_query" value="{{ request('search_query') }}">
                <button type="submit" class="btn btn-primary btn-block mt-2">Filter</button>
            </div>
        </div>
    </form>
</div>


<!-- Card Container untuk Search dan Tabel -->
<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Data Gaji Pegawai</h6>
    </div>
    <div class="card-body">
        <!-- Form Pencarian Nama Pegawai -->
       <div class="row">
        <div class="col-md-6">
            <div class=" mb-3">
                <form action="{{ route('manager.cekgaji.search', ['id' => $selectedOutlet['id']]) }}" method="GET">
                    <div class="form-row align-items-center">
                        <div class="col-md-12 col-lg-6">
                            <label for="search_query">Cari Pegawai:</label>
                            <div class="input-group">
                                <input 
                                    type="text" 
                                    id="search_query" 
                                    name="search_query" 
                                    class="form-control" 
                                    value="{{ request('search_query') }}" 
                                    placeholder="Nama Pegawai">
                                <div class="input-group-append">
                                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </button>
    
                                    @if(request('search_query')) <!-- Check if there is a search query -->
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
              <a href="{{ route('manager.cekgaji.cetakpdf', ['id' => $selectedOutlet['id'], 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-danger">
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
                    @if($dataGaji->count() > 0)
                        @foreach ($dataGaji as $gaji)
                            <tr>
                                <td>{{ $gaji->nama_pegawai }}</td>
                                <td>{{ $gaji->nama_pekerjaan }}</td>
                                <td>
                                    <span
                                        style="cursor: pointer"
                                        data-toggle="tooltip" 
                                        title="{{ $gaji->tanggal_shift }}"
                                        class="badge badge-primary">
                                        {{ $gaji->jumlah_shift }} Hari
                                    </span>
                                </td>
                                </td>
                                <td>Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">Data tidak ditemukan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection


@push('myscript')
<script>
    function resetSearch() {
        const searchInput = document.getElementById('search_query');
        
        // Clear the search input
        searchInput.value = '';
    
        // Submit the form with the cleared search query but keep the other parameters
        const form = searchInput.closest('form');
        form.submit();
    }

    $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); // Inisialisasi semua tooltip
});

    </script>
@endpush