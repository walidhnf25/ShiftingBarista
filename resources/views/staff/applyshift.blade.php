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

<div class="row mb-3">
    <div class="col-md-6">
        <form action="{{ route('filterJadwalShift') }}" method="GET" id="filterForm" class="d-flex gap-3">
            <!-- Filter Outlet -->
            <select name="id_outlet" id="id_outlet" class="form-control">
                <option value="">ALL OUTLET</option>
                @foreach ($outletMapping as $id => $outletName)
                    <option value="{{ $id }}" {{ request('id_outlet') == $id ? 'selected' : '' }}>
                        {{ $outletName }}
                    </option>
                @endforeach
            </select>

            <!-- Filter Tipe Pekerjaan -->
            <select name="id_tipe_pekerjaan" id="id_tipe_pekerjaan" class="form-control mx-3">
                <option value="">ALL TIPE PEKERJAAN</option>
                @foreach ($TipePekerjaan as $tipe)
                    <option value="{{ $tipe->id }}" {{ request('id_tipe_pekerjaan') == $tipe->id ? 'selected' : '' }}>
                        {{ $tipe->tipe_pekerjaan }}
                    </option>
                @endforeach
            </select>
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
                        
                    @else
                        @if ($availRegister === 'No')
                            
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
                            <td colspan="7" class="text-center">Belum ada jadwal shift dipilih</td>
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

    document.getElementById("id_outlet").addEventListener("change", function () {
        // Reset filter tipe pekerjaan saat outlet berubah
        document.getElementById("id_tipe_pekerjaan").selectedIndex = 0;

        // Submit form
        document.getElementById("filterForm").submit();
    });

    document.getElementById("id_tipe_pekerjaan").addEventListener("change", function () {
        // Submit form saat tipe pekerjaan dipilih
        document.getElementById("filterForm").submit();
    });

    // Sembunyikan alert setelah beberapa detik
    setTimeout(function () {
        $('.alert').fadeOut('slow');
    }, 2000);
});
</script>