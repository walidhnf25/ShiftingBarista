@extends('layouts.tabler')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tipe Pekerjaan</h1>
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
                <i class="fa fa-plus"></i> Tambah Tipe Pekerjaan
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Tipe Pekerjaan</th>
                        <th>Minimum Fee</th>
                        <th>Average Fee</th>
                        <th>Maximum Fee</th>
                        <th>Control</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($TipePekerjaan as $d)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $d->tipe_pekerjaan }}</td>
                            <td>Rp {{ number_format($d->min_fee, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($d->avg_fee, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($d->max_fee, 0, ',', '.') }}</td>
                            <td>
                                <a href="#" class="edit btn btn-info btn-sm btn-circle" id="{{ $d->id }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('tipepekerjaan.deleteTipePekerjaan', $d->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-circle btn-sm delete-confirm">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tipe pekerjaan belum ditambahkan</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>



    <div class="modal fade" id="modal-inputpekerjaan" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Tipe Pekerjaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('tipe_pekerjaan.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3"> <!-- Corrected class name -->
                                    <label for="tipe_pekerjaan">Tipe Pekerjaan</label>
                                    <input type="text" class="form-control" id="tipe_pekerjaan" name="tipe_pekerjaan"
                                        placeholder="Tipe Pekerjaan">
                                </div>
                            </div>
                            <div class="col-12 my-2">
                                <strong class="h5 font-weight-bold text-dark"> Fee Pegawai </strong>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3"> <!-- Corrected class name -->
                                    <label for="min_fee">Minimum Fee</label>
                                    <input type="number" class="form-control" id="min_fee" name="min_fee"
                                        placeholder="Minimum Fee Pegawai">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3"> <!-- Corrected class name -->
                                    <label for="avg_fee">Average Fee</label>
                                    <input type="number" class="form-control" id="avg_fee" name="avg_fee"
                                        placeholder="Average Fee Pegawai">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3"> <!-- Corrected class name -->
                                    <label for="max_fee">Maximum Fee</label>
                                    <input type="number" class="form-control" id="max_fee" name="max_fee"
                                        placeholder="Maximum Fee Pegawai">
                                </div>
                            </div>
                            <div class="col-12 my-2">
                                <strong class="h5 font-weight-bold text-dark"> Pendapatan Outlet </strong>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="pendapatan_batas_atas">Batas Atas</label>
                                    <input type="number" class="form-control" id="pendapatan_batas_atas"
                                        name="pendapatan_batas_atas" placeholder="Batas Atas Penghasilan">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3"> <!-- Corrected class name -->
                                    <label for="pendapatan_batas_bawah">Batas Bawah</label>
                                    <input type="number" class="form-control" id="pendapatan_batas_bawah"
                                        name="pendapatan_batas_bawah" placeholder="Batas Bawah Penghasilan">
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
                    url: "/editTipePekerjaan",
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
