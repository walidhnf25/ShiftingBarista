@extends('layouts.tabler')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-gray-800">Gaji Anda</h1>
    </div>
    <p class="h6 mb-4">Mohon perhatikan gaji yang anda dapatkan, jika ada kesalahan bisa langsung di pertanyakan </p>

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
    {{-- 
<div class="row"> 
    <div class="col mb-3">
        <form action="{{ route('filterJadwalShift') }}" method="GET"> <!-- Ganti ke GET jika filter di URL -->
            @csrf
            <div class="btn-group">
                <select name="id_outlet" class="form-control" onchange="this.form.submit()" @if ($availRegister === 'No') disabled @endif>
                    <option value="">ALL OUTLET</option>
                    @foreach ($outletMapping as $id => $outletName)
                        <option value="{{ $id }}" @if (request('id_outlet') == $id) selected @endif>
                            {{ $outletName }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div> --}}

    <div class="row">
        <div class="col mb-3">
            <form> <!-- Ganti ke GET jika filter di URL -->
                @csrf
                <div class="btn-group">
                    <select name="id_outlet" class="form-control">
                        <option value="">ALL OUTLET</option>
                        {{-- @foreach ($outletMapping as $id => $outletName)
                        <option value="{{ $id }}" @if (request('id_outlet') == $id) selected @endif>
                            {{ $outletName }}
                        </option>
                    @endforeach --}}
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">


        <div class="card-body">
            <!-- Filter Tanggal -->
            <div class="mb-4">
                <form method="GET">
                    <div class="form-row align-items-center">
                        <div class="col-md-4">
                            <label for="start_date">Tanggal Mulai:</label>
                            <input type="date" id="start_date" name="start_date" class="form-control"
                                value="{{ request('start_date', date('Y-m-01')) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date">Tanggal Selesai:</label>
                            <input type="text" id="end_date" name="end_date" class="form-control"
                                value="{{ now()->format('Y-m-d H:i:s') }}" readonly>
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
        </div>
    </div>
@endsection
