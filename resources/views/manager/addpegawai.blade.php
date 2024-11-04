@extends('layouts.tabler')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Pegawai</h1>
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
            <form action="{{ route('addpegawai.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <!-- Name -->
                    <div class="form-group col-md-6">
                        <label for="name">Nama Pegawai</label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Masukkan nama pegawai">
                    </div>


                    <!-- Username -->
                    <div class="form-group col-md-6">
                        <label for="username">Username Pegawai</label>
                        <input type="username" class="form-control" id="username" name="username"
                            placeholder="Masukkan username pegawai" required>
                    </div>
                </div>

                <div class="form-row">

                    <!-- Email -->
                    <div class="form-group col-md-6 ">
                        <label for="email">Email Pegawai</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Masukkan email pegawai" required>
                    </div>

                    <!-- Password -->
                    <div class="form-group col-md-6">
                        <label for="password">Password Pegawai</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Masukkan password" required>
                    </div>

                </div>

                <div class="form-row">
                    <!-- Role -->
                    <div class="form-group col-12">
                        <label for="role">Role Pegawai</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="" disabled selected>Pilih Role</option>
                            <option value="Manager">Manager</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary mt-3">Tambah Pegawai</button>
            </form>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mt-3" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2 class="h4 mt-5">Data Pegawai</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Nama</th>
                <th scope="col">Email</th>
                <th scope="col">Role</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <!-- Tombol untuk Read, Update, Delete -->
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                            data-target="#ReadModal{{ $user->id }}">
                            Read
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                            data-target="#EditModal{{ $user->id }}">
                            Edit
                        </button>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="EditModal{{ $user->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="EditModal{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Pegawai <strong>{{ $user->name }}</strong></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('addpegawai.update', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-row">
                                                <!-- Name -->
                                                <div class="form-group col-md-6">
                                                    <label for="name">Nama Pegawai</label>
                                                    <input type="text" class="form-control" id="name" name="name"
                                                        value="{{ $user->name }}">
                                                </div>

                                                {{-- Username --}}
                                                <div class="form-group col-md-6">
                                                    <label for="username">Username Pegawai</label>
                                                    <input type="text" class="form-control" id="username"
                                                        name="username" value="{{ $user->username }}">
                                                </div>

                                                <!-- Email -->
                                                <div class="form-group col-md-6">
                                                    <label for="email">Email Pegawai</label>
                                                    <input type="email" class="form-control" id="email"
                                                        name="email" value="{{ $user->email }}">
                                                </div>

                                                {{-- Role --}}
                                                <div class="form-group col-md-6">
                                                    <label for="role">Role Pegawai</label>
                                                    <select class="form-control" id="role" name="role">
                                                        <option value="" disabled selected>Pilih Role</option>
                                                        <option value="Manager"
                                                            {{ $user->role == 'Manager' ? 'selected' : '' }}>Manager
                                                        </option>
                                                        <option value="Staff"
                                                            {{ $user->role == 'Staff' ? 'selected' : '' }}>Staff</option>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('addpegawai.destroy', $user->email) }}" method="POST" class="d-inline">
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

    <!-- Read Modal -->
    <div class="modal fade" id="ReadModal{{ $user->id }}" tabindex="-1" aria-labelledby="ReadModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pegawai <strong>{{ $user->name }}</strong></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p><strong>Nama :</strong> {{ $user->name }} </p>
                    <p><strong>Email :</strong> {{ $user->email }} </p>
                    <p><strong>username :</strong> {{ $user->username }} </p>
                    <p><strong>Role :</strong> {{ $user->role }} </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="EditModal{{ $user->id }}" tabindex="-1" aria-labelledby="EditModal"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pegawai <strong>{{ $user->name }}</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('addpegawai.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-row">
                            <!-- Name -->
                            <div class="form-group col-md-6">
                                <label for="name">Nama Pegawai</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $user->name }}">
                            </div>

                            {{-- Username --}}
                            <div class="form-group col-md-6">
                                <label for="username">Username Pegawai</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="{{ $user->username }}">
                            </div>

                            <!-- Email -->
                            <div class="form-group col-md-6">
                                <label for="email">Email Pegawai</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ $user->email }}">
                            </div>

                            {{-- Role --}}
                            <div class="form-group col-md-6">
                                <label for="role">Role Pegawai</label>
                                <select class="form-control" id="role" name="role">
                                    <option value="" disabled selected>Pilih Role</option>
                                    <option value="Manager" {{ $user->role == 'Manager' ? 'selected' : '' }}>Manager
                                    </option>
                                    <option value="Staff" {{ $user->role == 'Staff' ? 'selected' : '' }}>Staff</option>
                                </select>
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
