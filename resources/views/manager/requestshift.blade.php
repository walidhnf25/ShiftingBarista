@extends('layouts.tabler')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">ACC Jadwal Shift</h1>
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

    <!-- Table Data ACC Shift -->
    <form action="{{ route('requestshift.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered table-striped" id="shiftTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jam Kerja</th>
                            <th>Hari</th>
                            <th>Tanggal</th>
                            <th>Tipe Pekerjaan</th>
                            <th>Staff</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwal_shift as $rs)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $rs->jamShift ? $rs->jamShift->jam_mulai . ' - ' . $rs->jamShift->jam_selesai : 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($rs->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                                <td>{{ $rs->tanggal }}</td>
                                <td>{{ $rs->tipePekerjaan ? $rs->tipePekerjaan->tipe_pekerjaan : 'N/A' }}</td>
                                <td>
                                    <select name="selected_user[{{ $rs->id }}]" class="form-control" style="width: 250px;">
                                        <option value="" disabled selected>Pilih User</option>
                                        @foreach ($rs->kesediaan as $kesediaan)
                                            <option value="{{ $kesediaan->user->id }}">
                                                {{ strtoupper($kesediaan->user->name) }}{{ $kesediaan->user->role === 'Manager' ? ' (MANAGER)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="checkbox" name="selected_shifts[]" value="{{ $rs->id }}"
                                        style="width: 20px; height: 20px; cursor: pointer;">
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
                <!-- Tombol ACC -->
                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary">ACC Shift</button>
                </div>
            </div>
        </div>
    </form>

@endsection
@push('myscript')
<script>
    // Show modal to add shift
    $("#btnTambahJamShift").click(function() {
        $('#modal-inputjamshift').modal('show');
    });

    // Delete confirmation
    $(".delete-confirm").click(function(e) {
        var form = $(this).closest('form');
        e.preventDefault();

        Swal.fire({
            title: '<span style="color:#f00">Apakah Anda Yakin?</span>',
            html: "<strong>Data ini akan dihapus secara permanen!</strong><br>Anda tidak akan bisa mengembalikan data setelah penghapusan.",
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
                confirmButton: 'btn btn-success',
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

    // Edit shift
    $(".edit").click(function() {
        var id = $(this).attr('id');
        $.ajax({
            type: "GET",
            url: "/editjamshift",
            cache: false,
            data: { id: id },
            success: function(respond) {
                $('#loadeditform').html(respond);
                $('#modal-ubahjamshift').modal('show');
            }
        });
    });

    // Inisialisasi DataTable dengan pengaturan tertentu
    $('#shiftTable').DataTable({
                paging: false,        // Nonaktifkan pagination
                searching: false,     // Nonaktifkan pencarian
                order: [[0, 'asc']],  // Default sorting pada kolom pertama (No)
                columnDefs: [
                    { targets: [6], orderable: false }, // Nonaktifkan sorting untuk kolom Jam Kerja, Hari, Outlet, Aksi
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

    // Fade out alert messages
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 2000);
</script>
@endpush