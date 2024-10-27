@extends('layouts.tabler')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Jadwal Shift</h1>
</div>

@if (session('success'))
<div class="alert alert-success col-md-6 col-lg-3" role="alert">
    {{ session('success') }}
</div>
@endif

<!-- Content Row -->
<div class="row">
    <div class="col-lg-12">
        <form action="" method="POST">
            @csrf
            <div class="form-row">
                <!-- JamShift -->
                <div class="form-group col-md-6">
                    <label for="jamShift">Jam Shift</label>
                    <select class="form-control" id="jamShift" name="id_jam_shift" required>
                        <option value="" disabled selected>Pilih Jam Shift</option>
                        <option value="1">08:00 - 12:00</option>
                        <option value="2">12:00 - 16:00</option>
                        <option value="3">16:00 - 20:00</option>
                    </select>
                    {{-- Harusnya gni kalau udah fetch dari table jamshift
                    <select class="form-control" id="jamShift" name="id_jam_shift" required>
                        <option value="" disabled selected>Pilih Jam Shift</option>
                        @foreach($jamShift as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->waktu_mulai }} - {{ $shift->waktu_selesai }}</option>
                        @endforeach --}}
                    </select>
                </div>

                <!-- Outlet -->
                <div class="form-group col-md-6">
                    <label for="outlet">Outlet</label>
                    <select class="form-control" id="outlet" name="id_outlet" required>
                        <option value="" disabled selected>Pilih Outlet</option>
                        <option value="1">Outlet A - Jalan Merdeka No.1</option>
                        <option value="2">Outlet B - Jalan Sudirman No.10</option>
                        <option value="3">Outlet C - Jalan Thamrin No.20</option>
                    </select>
                    {{-- outlet aktifin aja lid kalau udah ada koneksi table nya
                    <select class="form-control" id="outlet" name="id_outlet" required>
                        <option value="" disabled selected>Pilih Outlet</option>
                        @foreach($outlet as $o)
                            <option value="{{ $o->id }}">{{ $o->nama }} - {{ $o->alamat }}</option>
                        @endforeach
                    </select> --}}
                </div>
            </div>

            <div class="form-row">
                <!-- Tipe Pekerjaan -->
                <div class="form-group col-md-6">
                    <label for="tipePekerjaan">Tipe Pekerjaan</label>
                    <select class="form-control" id="tipePekerjaan" name="id_tipe_pekerjaan" required>
                        <option value="" disabled selected>Pilih Tipe Pekerjaan</option>
                        <option value="1">Barista</option>
                        <option value="2">Kasir</option>
                        <option value="3">Kitchen Helper</option>
                    </select>
                    {{-- <select class="form-control" id="tipePekerjaan" name="id_tipe_pekerjaan" required>
                        <option value="" disabled selected>Pilih Tipe Pekerjaan</option>
                        @foreach($tipePekerjaan as $tipe)
                            <option value="{{ $tipe->id }}">{{ $tipe->nama_pekerjaan }}</option>
                        @endforeach
                    </select> --}}
                </div>

                <!-- Tanggal -->
                <div class="form-group col-md-6">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="hari_tanggal" required>
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
                    <th>Jam Shift</th>
                    <th>Outlet</th>
                    <th>Tipe Pekerjaan</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>08:00 - 16:00</td>
                    <td>Outlet A - Jalan Merdeka No.1</td>
                    <td>Barista</td>
                    <td>2024-11-01</td>
                    <td>
                        <!-- Tombol untuk Read, Update, Delete -->
                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#EditModal">
                            Edit
                        </button>
                        {{-- Fungsi di action --}}
                        <form action="" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm delete-confirm" id="btnDelete" >Delete</button>
                        </form>
                    </td>
                </tr>
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
</script>
@endpush



<!-- Edit Modal -->
<div class="modal fade" id="EditModal" tabindex="-1" aria-labelledby="EditModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jadwal Shift <strong>1 </strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-row">
                        <!-- Name -->
                        <div class="form-group col-md-6">
                            <label for="jamShift">Jam Shift</label>
                            <select class="form-control" id="jamShift" name="jamShift" required>
                                <option value="1">08:00 - 12:00</option>
                                <option value="2">12:00 - 16:00</option>
                                <option value="3">16:00 - 20:00</option>
                            </select>
                        </div>

                        {{-- Username --}}
                        <div class="form-group col-md-6">
                             <label for="outlet">Outlet </label>
                             <select class="form-control" id="outlet" name="outlet" >
                                <option value="1">Outlet A - Jalan Merdeka No.1</option>
                                <option value="2">Outlet B - Jalan Sudirman No.10</option>
                                <option value="3">Outlet C - Jalan Thamrin No.20</option>
                            </select>
                        </div>

                        <!-- Email -->
                        <div class="form-group col-md-6">
                            <label for="tipePekerjaan">Tipe Pekerjaan</label>
                            <select class="form-control" id="tipePekerjaan" name="tipePekerjaan" >
                                <option value="1">Barista</option>
                                <option value="2">Kasir</option>
                                <option value="3">Kitchen Helper</option>
                            </select>
                        </div>

                          <!-- Email -->
                          <div class="form-group col-md-6">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="Barista">
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
</div>