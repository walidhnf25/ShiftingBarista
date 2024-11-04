@extends('layouts.tabler')

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h2 mb-0 text-gray-800">Terapkan Shift Anda</h1>
</div>
<p class="h6 mb-4">Mohon Perhatikan shift yang anda pilih dengan seksama !!</p>

<div class="row">
    <div class="col-md-6 col-lg-6">
        <h2 class="h4 mb-2">Pilihan Jadwal Shift</h2>
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jam Kerja</th>
                        <th>Pekerjaan</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jadwal_shift as $shift)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $shift->jam_kerja }}</td>
                            <td>{{ $shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A' }}</td>
                            <td>{{ $shift->tanggal }}</td>
                            <td>
                                <button type="submit" class="btn btn-outline-primary">+</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6 col-lg-6 d-flex flex-column">
        <h2 class="h4 mb-2">Jadwal Shift yang Anda Pilih</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jam Kerja</th>
                        <th>Pekerjaan</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center">Anda belum memilih tanda shift</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Siap Acc</button>
        </div>
    </div>
</div>
@endsection