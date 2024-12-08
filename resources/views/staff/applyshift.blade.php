@extends('layouts.tabler')

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h2 mb-0 text-gray-800">Reservasi Shift Anda</h1>
</div>
<p class="h6 mb-3">Mohon perhatikan shift yang anda pilih dengan seksama!!</p>

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

<div class="row"> 
    <div class="col mb-3">
        <form action="{{ route('filterJadwalShift') }}" method="GET"> <!-- Ganti ke GET jika filter di URL -->
            @csrf
            <div class="btn-group">
                <select name="id_outlet" class="form-control" onchange="this.form.submit()" @if($availRegister === 'No') disabled @endif>
                    <option value="">ALL OUTLET</option>
                    @foreach ($outletMapping as $id => $outletName)
                        <option value="{{ $id }}" @if(request('id_outlet') == $id) selected @endif>
                            {{ $outletName }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>


<div class="row">
    <div class="col-md-12 col-lg-12">
        <h2 class="h4 mb-2">PILIHAN JADWAL SHIFT</h2>
        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
            <table class="table table-bordered table-striped" id="shiftTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jam Kerja</th>
                        <th>Pekerjaan</th>
                        <th>Hari</th>
                        <th>Tanggal</th>
                        <th>Outlet</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="shiftData">
                    @if ($jadwal_shift->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center">Jadwal Shift Kosong.</td>
                        </tr>
                    @else
                        @if ($availRegister === 'No')
                            <tr>
                                <td colspan="7" class="text-center">Anda sudah memilih jadwal shift.</td>
                            </tr>
                        @else
                            @foreach ($jadwal_shift as $shift)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $shift->jamShift ? $shift->jamShift->jam_mulai . ' - ' . $shift->jamShift->jam_selesai : 'N/A' }}
                                    </td>
                                    <td>{{ $shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($shift->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                                    <td>{{ $shift->tanggal }}</td>
                                    <td>{{ $outletMapping[$shift->id_outlet] ?? 'Outlet Not Found' }}</td>
                                    <td>
                                        <a href="{{ route('getJadwalShift', $shift->id) }}" class="btn btn-outline-primary">+</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row"> 
    <div class="col-md-12 col-lg-12 d-flex flex-column mt-3">
        <h2 class="h4 mb-2">RESERVASI JADWAL SHIFT</h2>
        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
            <table class="table table-bordered table-striped" id="shiftTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jam Kerja</th>
                        <th>Pekerjaan</th>
                        <th>Hari</th>
                        <th>Tanggal</th>
                        <th>Outlet</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="selectedShiftData">
                    @if ($availRegister === 'No' && $kesediaanShifts->isNotEmpty())
                        @foreach ($kesediaanShifts as $shift)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $shift->jamShift ? $shift->jamShift->jam_mulai . ' - ' . $shift->jamShift->jam_selesai : 'N/A' }}
                                </td>
                                <td>{{ $shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($shift->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                                <td>{{ $shift->tanggal }}</td>
                                <td>{{ $outletMapping[$shift->id_outlet] ?? 'Outlet Not Found' }}</td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger remove-from-cache" data-id="{{ $shift->id }}" disabled>-</button>
                                </td>
                            </tr>
                        @endforeach
                    @elseif ($availRegister !== 'No' && count($cachedJadwalShifts) > 0)
                        @foreach ($cachedJadwalShifts as $shift)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $shift->jamShift ? $shift->jamShift->jam_mulai . ' - ' . $shift->jamShift->jam_selesai : 'N/A' }}
                                </td>
                                <td>{{ $shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($shift->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                                <td>{{ $shift->tanggal }}</td>
                                <td>{{ $outletMapping[$shift->id_outlet] ?? 'Outlet Not Found' }}</td>
                                <td>
                                    <form action="{{ route('removeFromCache', $shift->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger remove-from-cache">-</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="placeholder-row">
                            <td colspan="7" class="text-center">Belum ada shift yang dipilih.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <form id="registrationForm" action="{{ route('kesediaan.store') }}" method="POST">
                @csrf
                <button type="button" class="btn btn-primary" id="registerButton" @if($availRegister === 'No') disabled @endif>Registrasi</button>
            </form>
        </div>
    </div>
</div>

@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function () {
    // Konfirmasi sebelum melakukan registrasi
    document.getElementById("registerButton").addEventListener("click", function () {
        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: "Data yang telah diregistrasikan tidak dapat direset!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Registrasi!',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika user mengonfirmasi, submit form
                document.getElementById("registrationForm").submit();
            }
        });
    });

    // Inisialisasi DataTable dengan pengaturan tertentu
    $('#shiftTable').DataTable({
        paging: false,        // Nonaktifkan pagination
        searching: false,     // Nonaktifkan pencarian
        order: [[0, 'asc']],  // Default sorting pada kolom pertama (No)
        columnDefs: [
            { targets: [1, 3, 5, 6], orderable: false }, // Nonaktifkan sorting untuk kolom Jam Kerja, Hari, Outlet, Aksi
            { targets: '_all', orderable: true }          // Aktifkan sorting untuk kolom lainnya
        ]
    });

    
    // Fungsi untuk menyortir kolom secara manual
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('shiftTable');
        const headers = table.querySelectorAll('th');
        const tbody = table.querySelector('tbody');

        headers.forEach((header, index) => {
            header.addEventListener('click', () => {
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const isAscending = header.classList.contains('ascending');
                const direction = isAscending ? -1 : 1;

                // Sort rows
                rows.sort((a, b) => {
                    let aText = a.cells[index].textContent.trim();
                    let bText = b.cells[index].textContent.trim();

                    // Jika kolom adalah tanggal, ubah menjadi objek Date
                    if (index === 3) { // Kolom tanggal (0-indexed)
                        aText = new Date(aText); // Konversi ke Date
                        bText = new Date(bText);
                    }

                    return aText > bText ? (1 * direction) : (-1 * direction);
                });

                // Toggle sorting class
                headers.forEach(h => h.classList.remove('ascending', 'descending'));
                header.classList.toggle('ascending', !isAscending);
                header.classList.toggle('descending', isAscending);

                // Append sorted rows
                rows.forEach(row => tbody.appendChild(row));
            });
        });
    });

    // Sembunyikan alert setelah beberapa detik
    setTimeout(function () {
        $('.alert').fadeOut('slow');
    }, 2000);
});
</script>