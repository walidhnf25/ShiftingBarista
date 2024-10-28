@extends('layouts.tabler')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Jadwal Shift</h1>
    </div>

    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ route('jadwal_shift.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <!-- Jam Shift Start -->
                    <div class="form-group col-md-6">
                        <label for="jam_mulai">Jam Mulai</label>
                        <select class="form-control" id="jam_mulai" name="jam_mulai" required>
                            <option value="" disabled selected>Pilih Jam Mulai</option>
                            @foreach ($jamShift as $d)
                                <option value="{{ $d->jam_mulai }}">{{ $d->jam_mulai }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jam Shift End -->
                    <div class="form-group col-md-6">
                        <label for="jam_selesai">Jam Selesai</label>
                        <select class="form-control" id="jam_selesai" name="jam_selesai" required>
                            <option value="" disabled selected>Pilih Jam Selesai</option>
                            @foreach ($jamShift as $d)
                                <option value="{{ $d->jam_selesai }}">{{ $d->jam_selesai }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Outlet Selection -->
                    <div class="form-group col-md-6">
                        <label for="outlet">Outlet</label>
                        <select class="form-control" id="outlet" name="outlet" required>
                            <option value="" disabled selected>Pilih Outlet</option>
                            <option value="Outlet A">Outlet A</option>
                            <option value="Outlet B">Outlet B</option>
                            <option value="Outlet C">Outlet C</option>
                        </select>
                    </div>

                    <!-- Date Selection -->
                    <div class="form-group col-md-6">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary mt-3">Tambah Jadwal Shift</button>
            </form>
        </div>
    </div>

    <!-- Table Data Jadwal Shift -->
    <div class="row mt-5">
        <div class="col-lg-12">
            <h2 class="h4 mb-4">Data Jadwal Shift</h2>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Outlet</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwal_shift as $d)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $d->jam_mulai }}</td>
                            <td>{{ $d->jam_selesai }}</td>
                            <td>{{ $d->outlet }}</td>
                            <td>{{ $d->tanggal }}</td>
                            <td>
                                <!-- Edit Button -->
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#EditModal{{ $d->id }}">
                                    Edit
                                </button>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="EditModal{{ $d->id }}" tabindex="-1" role="dialog" aria-labelledby="EditModalLabel{{ $d->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="EditModalLabel{{ $d->id }}">Edit Jadwal Shift</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('jadwalshift.update', $d->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <!-- Jam Mulai -->
                                                            <label for="jam_mulai">Jam Mulai</label>
                                                            <select class="form-control" id="jam_mulai" name="jam_mulai" required>
                                                                <option value="" disabled selected>Pilih Jam Mulai</option>
                                                                @foreach ($jamShift as $jam)
                                                                    <option value="{{ $jam->jam_mulai }}" {{ $d->jam_mulai == $jam->jam_mulai ? 'selected' : '' }}>
                                                                        {{ $jam->jam_mulai }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <!-- Jam Selesai -->
                                                            <label for="jam_selesai">Jam Selesai</label>
                                                            <select class="form-control" id="jam_selesai" name="jam_selesai" required>
                                                                <option value="" disabled selected>Pilih Jam Selesai</option>
                                                                @foreach ($jamShift as $jam)
                                                                    <option value="{{ $jam->jam_selesai }}" {{ $d->jam_selesai == $jam->jam_selesai ? 'selected' : '' }}>
                                                                        {{ $jam->jam_selesai }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <!-- Outlet -->
                                                            <label for="outlet">Outlet</label>
                                                            <select class="form-control" id="outlet" name="outlet" required>
                                                                <option value="" disabled selected>Pilih Outlet</option>
                                                                <option value="Outlet A" {{ $d->outlet == 'Outlet A' ? 'selected' : '' }}>Outlet A</option>
                                                                <option value="Outlet B" {{ $d->outlet == 'Outlet B' ? 'selected' : '' }}>Outlet B</option>
                                                                <option value="Outlet C" {{ $d->outlet == 'Outlet C' ? 'selected' : '' }}>Outlet C</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group col-md-6">
                                                            <!-- Tanggal -->
                                                            <label for="tanggal">Tanggal</label>
                                                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ $d->tanggal }}">
                                                        </div>
                                                    </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                                </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Form -->
                                <form action="{{ route('jadwal_shift.destroy', $d->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm delete-confirm">Delete</button>
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
    // Menghilangkan alert setelah beberapa detik
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 2000);
</script>
@endpush