    @extends('layouts.tabler')

    @section('content')
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Tambah Jadwal Shift</h1>
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

        <!-- Content Row -->
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('jadwal_shift.store', ['id' => $selectedOutlet['id']]) }}" method="POST"enctype="multipart/form-data">
                    @csrf
                    <div class="form-row">
                        <!-- Jam Kerja -->
                        <div class="form-group col-md-6">
                            <label for="id_jam">Jam Kerja</label>
                            <select class="form-control" id="id_jam" name="id_jam">
                                <option value="" disabled selected>Pilih Jam Kerja</option>
                                @foreach ($jamShift as $d)
                                    <option value="{{ $d->id }}">{{ $d->jam_mulai }} -
                                        {{ $d->jam_selesai }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Outlet Selection -->
                        <div class="form-group col-md-6">
                            <label for="outlet">Tipe Pekerjaan</label>
                            <select class="form-control" id="id_tipe_pekerjaan" name="id_tipe_pekerjaan" required>
                                <option value="" disabled selected>Pilih Tipe Pekerjaan</option>
                                @foreach ($TipePekerjaan as $d)
                                    <option value="{{ $d->id }}">{{ $d->tipe_pekerjaan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tanggal Mulai -->
                        <div class="form-group col-md-6">
                            <label for="tanggal">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                        </div>

                        <!-- Tanggal Akhir -->
                        <div class="form-group col-md-6">
                            <label for="tanggal">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Tambah Jadwal Shift</button>
                </form>
            </div>
        </div>

        <!-- Table Data Jadwal Shift -->
        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="h4 mb-3">Data Jadwal Shift</h2>
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <form action="{{ route('outlet.jadwalshift', ['id' => $selectedOutlet['id'] ?? 0]) }}" method="GET">
                            <div class="btn-group">
                                <select name="id_periode" class="form-control" onchange="this.form.submit()">
                                    <option value="">Pilih Periode</option>
                                    @foreach ($periode_gaji as $periode)
                                        <option value="{{ $periode->id }}" {{ request('id_periode') == $periode->id ? 'selected' : '' }}>
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
                            <!-- Ensure you're using this variable for each loop iteration -->
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
                                    {{ $shift->user ? strtoupper($shift->user->name) . ($shift->user->role === 'Manager' ? ' (MANAGER)' : '') : '-' }}
                                </td>
                                <td>
                                    <!-- Edit Button -->
                                    <!-- <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                        data-target="#EditModal{{ $shift->id }}">
                                        Edit
                                    </button> -->

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="EditModal{{ $shift->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="EditModalLabel{{ $shift->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="EditModalLabel{{ $shift->id }}">Edit
                                                        Jadwal Shift</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('jadwalshift.update', $shift->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row">
                                                            <!-- Jam Kerja -->
                                                            <div class="form-group col-md-6">
                                                                <label for="jam_kerja">Jam Kerja</label>
                                                                <select class="form-control" id="id_jam" name="id_jam">
                                                                    <option value="" disabled selected>Pilih Jam Kerja</option>
                                                                    @foreach ($jamShift as $jam)
                                                                        <option value="{{ $jam->id }}" 
                                                                            {{ $jam->id == $shift->id_jam ? 'selected' : '' }}>
                                                                            {{ $jam->jam_mulai }} - {{ $jam->jam_selesai }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <!-- Tanggal Mulai -->
                                                            <div class="form-group col-md-6">
                                                                <label for="tipe_pekerjaan">Tipe Pekerjaan</label>
                                                                <select class="form-control" id="id_tipe_pekerjaan"
                                                                    name="id_tipe_pekerjaan" required>
                                                                    <option value="" disabled>Pilih Tipe Pekerjaan
                                                                    </option>
                                                                    @foreach ($TipePekerjaan as $type)
                                                                        <option value="{{ $type->id }}"
                                                                            {{ $type->id == $shift->id_tipe_pekerjaan ? 'selected' : '' }}>
                                                                            {{ $type->tipe_pekerjaan }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="form-group col-md-6">
                                                                <label for="id_user">User</label>
                                                                <select class="form-control" id="id_user" name="id_user" required>
                                                                    <option value="" disabled selected>Pilih User</option>
                                                                    @foreach ($User as $type)
                                                                        <option value="{{ $type->id }}" {{ $type->id == $shift->id_user ? 'selected' : '' }}>
                                                                            {{ strtoupper($type->name) }}{{ $type->role === 'Manager' ? ' (MANAGER)' : '' }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <!-- Tanggal -->
                                                            <div class="form-group col-md-6">
                                                                <label for="tanggal">Tanggal Mulai</label>
                                                                <input type="date" class="form-control" id="tanggal"
                                                                    name="tanggal" value="{{ $shift->tanggal }}">
                                                            </div>

                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save
                                                                changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Form -->
                                    <form action="{{ route('jadwal_shift.destroy', $shift->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm delete-confirm"
                                            id="btnDelete">Delete</button>
                                    </form>
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
            // Delete confirmation
            $(".delete-confirm").click(function(e) {
                var form = $(this).closest('form');
                e.preventDefault();

                Swal.fire({
                    title: '<span style="color:#f00">Apakah Anda Yakin?</span>',
                    html: "<strong>Data Pegawai ini akan dihapus secara permanen!</strong><br>Anda tidak akan bisa mengembalikan data setelah penghapusan.",
                    icon: 'warning',
                    iconColor: '#ff6b6b',
                    showCancelButton: true,
                    background: '#f7f7f7',
                    backdrop: `
                rgba(0, 0, 0, 0.4)
                url("https://cdn.pixabay.com/photo/2016/11/18/15/07/red-alert-1837455_960_720.png")
                left top
                no-repeat
            `,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batalkan',
                    customClass: {
                        popup: 'animated zoomIn faster',
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-danger',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();

                        Swal.fire({
                            title: 'Info!',
                            text: 'Data berhasil dihapus.',
                            icon: 'success',
                            background: '#f7f7f7',
                            customClass: {
                                popup: 'animated bounceIn faster',
                            },
                            showConfirmButton: false,
                            timer: 1500,
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
                    { targets: [1, 3, 7], orderable: false }, // Nonaktifkan sorting untuk kolom Jam Kerja, Hari, Outlet, Aksi
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

            // Menghilangkan alert setelah beberapa detik
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 2000);

            function resetSearch() {
                const url = new URL(window.location.href);
                const id_periode = url.searchParams.get('id_periode');
                // Buat URL baru tanpa parameter search_query
                const newUrl = `${url.origin}${url.pathname}?id_periode=${id_periode}`;
                window.location.href = newUrl;
            }

            $(document).ready(function() {
                $('[data-toggle="tooltip"]').tooltip(); // Inisialisasi tooltip
            });
        </script>
    @endpush
