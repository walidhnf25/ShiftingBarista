    @extends('layouts.tabler')

    @section('content')
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Tukar Jadwal Shift</h1>
        </div>

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
                <form action="{{ route('filterJadwalShifts') }}" method="GET">
                    @csrf
                    <div class="btn-group">
                        <select name="id_outlet" class="form-control" onchange="this.form.submit()">
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

        <!-- Table Data Jadwal Shift -->
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered table-striped" id="shiftTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jam Kerja</th>
                            <th>Pekerjaan</th>
                            <th>Outlet</th>
                            <th>Hari</th>
                            <th>Tanggal</th>
                            <th>Pegawai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                        @foreach ($jadwal_shift as $shift)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $shift->jamShift ? $shift->jamShift->jam_mulai . ' - ' . $shift->jamShift->jam_selesai : 'N/A' }}
                                </td>
                                <td>{{ $shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A' }}</td>
                                <td>{{ $outletMapping[$shift->id_outlet] }}</td>
                                <td>{{ \Carbon\Carbon::parse($shift->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                                <td>{{ $shift->tanggal }}</td>
                                <td>
                                    <select class="form-control select-user" data-shift-id="{{ $shift->id }}" required>
                                        <option value="" disabled selected>Pilih User</option>
                                        @foreach ($User as $type)
                                            <option value="{{ $type->id }}" {{ $type->id == $shift->id_user ? 'selected' : '' }}>
                                                {{ strtoupper($type->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <!-- Button Ubah User -->
                                    <button type="button" class="btn btn-warning btn-sm btn-update-user" data-shift-id="{{ $shift->id }}">
                                        Ubah User
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @endsection

    @push('myscript')
        <script>
            $(document).ready(function () {
                // Update User Button Click Handler
                $('.btn-update-user').on('click', function () {
                    const shiftId = $(this).data('shift-id'); // Ambil ID shift
                    const userId = $(`.select-user[data-shift-id="${shiftId}"]`).val(); // Ambil ID user yang dipilih

                    if (!userId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pilih User!',
                            text: 'Silakan pilih user terlebih dahulu.',
                            confirmButtonText: 'OK',
                        });
                        return;
                    }

                    // Tampilkan dialog konfirmasi sebelum mengupdate
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data jadwal shift akan diperbarui dengan user yang dipilih.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Update!',
                        cancelButtonText: 'Batalkan',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Kirim permintaan AJAX jika dikonfirmasi
                            $.ajax({
                                url: "{{ route('jadwalshift.update-user') }}",
                                method: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    id_shift: shiftId,
                                    id_user: userId,
                                },
                                success: function (response) {
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: response.message,
                                            timer: 1500,
                                            showConfirmButton: false,
                                        }).then(() => location.reload());
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal!',
                                            text: 'Gagal memperbarui user.',
                                        });
                                    }
                                },
                                error: function () {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Kesalahan!',
                                        text: 'Terjadi kesalahan saat memperbarui user.',
                                    });
                                },
                            });
                        }
                    });
                });

                // Inisialisasi DataTable dengan pengaturan tertentu
                $('#shiftTable').DataTable({
                            paging: false,        // Nonaktifkan pagination
                            searching: false,     // Nonaktifkan pencarian
                            order: [[0, 'asc']],  // Default sorting pada kolom pertama (No)
                            columnDefs: [
                                { targets: [7], orderable: false }, // Nonaktifkan sorting untuk kolom Jam Kerja, Hari, Outlet, Aksi
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
                                        if (index === 5) { // Kolom tanggal (0-indexed)
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
            });
        </script>
    @endpush
