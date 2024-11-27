@extends('layouts.tabler')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Waktu Shift</h1>
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

    <!-- Add Shift Button -->
    <div class="row mb-3">
        <div class="col-md-3">
            <a href="#" class="btn btn-primary" id="btnTambahJamShift">
                <i class="fa fa-plus"></i> Tambah Jam Shift
            </a>
        </div>
    </div>

    <!-- Shift Table -->
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jam mulai</th>
                        <th>Jam selesai</th>
                        <th>Durasi</th>
                        <th>Outlet</th>
                        <th>Control</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jamShift as $d)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $d->jam_mulai }}</td>
                        <td>{{ $d->jam_selesai }}</td>
                        <td>{{ $d->durasi }}</td>
                        <td>{{ $outletMapping[$d->id_outlet] }}</td>
                        <!-- Tombol edit dan delete -->
                        <td>
                            <a href="#" class="edit btn btn-info btn-sm btn-circle"
                                id="{{ $d->id }}">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form action="{{ route('jamshift.deleteJamShift', $d->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                <button type="button" class="btn btn-danger btn-circle btn-sm delete-confirm">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Jam Shift belum ditambahkan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<!-- Modal - Tambah Shift -->
<div class="modal fade" id="modal-inputjamshift" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jam Shift</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('jam_shift.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="jam_mulai">Jam Mulai</label>
                                <input type="time" class="form-control" id="jamMulai" name="jam_mulai" placeholder="Jam Mulai">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="jam_selesai">Jam Selesai</label>
                                <input type="time" class="form-control" id="jamSelesai" name="jam_selesai" placeholder="Jam Selesai">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="jam_selesai">Outlet</label>
                                <select class="form-control" id="id_outlet" name="id_outlet" required>
                                    <option value="" disabled selected>Pilih Outlet</option>
                                    @foreach ($apiOutlet as $item)
                                        <option value="{{ $item['id'] }}">{{ $item['outlet_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary flex-grow-1">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal - Edit Shift -->
<div class="modal modal-blur fade" id="modal-ubahjamshift" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jam Shift</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="loadeditform"></div>
        </div>
    </div>
</div>
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

    // Fade out alert messages
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 2000);
</script>
@endpush
