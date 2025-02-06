@extends('layouts.tabler')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Periode Gaji </h1>
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
    <div class="row mb-3">
        <div class="col-md-3">
            <a href="#" class="btn btn-primary" id="btnTambahTipePekerjaan">
                <i class="fa fa-plus"></i> Tambah Periode Gaji
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Periode</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Akhir</th>
                            <th>Jumlah Hari</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($periodegaji as $pg)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $pg->nama_periode_gaji }}</td>
                                <td>{{ $pg->tgl_mulai }}</td>
                                <td>{{ $pg->tgl_akhir }}</td>
                                <td>{{ \Carbon\Carbon::parse($pg->tgl_mulai)->diffInDays(\Carbon\Carbon::parse($pg->tgl_akhir)) }}
                                    Hari</td>
                                <td>
                                    <a href="#" class="edit btn btn-info btn-sm btn-circle" 
                                        id="{{ $pg->id }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('periodegaji.delete', $pg->id) }}" method="POST"
                                        style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-circle btn-sm delete-confirm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal Store -->
    <div class="modal fade" id="modal-inputpekerjaan" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Rentang Periode Gaji</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('periodegaji.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3"> <!-- Corrected class name -->
                                    <label for="nama_periode_gaji">Nama Periode</label>
                                    <input type="text" class="form-control" id="nama_periode_gaji"
                                        name="nama_periode_gaji" value="{{ old('nama_periode_gaji') }}">
                                    @error('nama_periode_gaji')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3"> <!-- Corrected class name -->
                                    <label for="tgl_mulai">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai"
                                        value="{{ old('tgl_mulai') }}" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3"> <!-- Corrected class name -->
                                    <label for="tgl_akhir">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir"
                                        value="{{ old('tgl_akhir') }}" />
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

    <div class="modal modal-blur fade" id="modal-ubahpekerjaan" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Tipe Pekerjaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="loadeditform">
                    <!-- Form will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(document).ready(function() {
            // Menampilkan modal untuk menambah tipe pekerjaan
            $("#btnTambahTipePekerjaan").click(function() {
                $('#modal-inputpekerjaan').modal('show');
            });

          

            // Konfirmasi penghapusan data
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

            // Mengedit tipe pekerjaan
            $(".edit").click(function() {
                var id = $(this).attr('id');
                $.ajax({
                    type: "GET",
                    url: "/editPeriodeGaji",
                    cache: false,
                    data: {
                        id: id
                    },
                    success: function(respond) {
                        console.log(respond); // Tambahkan ini untuk debugging
                        $('#loadeditform').html(respond);
                        $('#modal-ubahpekerjaan').modal('show');
                    }
                });
            });

            // Menghilangkan alert setelah beberapa detik
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 2000);
        });
    </script>
@endpush
