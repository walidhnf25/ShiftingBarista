@extends('layouts.tabler')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Pegawai</h1>
</div>

@if (session('success'))
<div class="alert alert-success col-md-6 col-lg-3" role="alert">
    {{ session('success') }}
</div>
@endif

<!-- Content Row -->
<div class="row">
    <div class="col-lg-12">
        <form action="{{ route('addpegawai.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <!-- Name -->
                <div class="form-group col-md-6">
                    <label for="name">Nama Pegawai</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama pegawai">
                </div>

                <!-- Email -->
                <div class="form-group col-md-6">
                    <label for="email">Email Pegawai</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email pegawai" required>
                </div>
            </div>

            <div class="form-row">
                <!-- Password -->
                <div class="form-group col-md-6">
                    <label for="password">Password Pegawai</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                </div>

                <!-- Role -->
                <div class="form-group col-md-6">
                    <label for="id">Role Pegawai</label>
                    <select class="form-control" id="id" name="id" required>
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

<table class="table table-striped">
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
        @foreach($users as $user)
        <tr>
            <th scope="row">{{ $loop->iteration }}</th>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td></td>
            <td>
                <!-- Tombol untuk Read, Update, Delete -->
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#ReadModal{{ $user->id }}">
                    Read
                </button>
                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#EditModal{{ $user->id }}">
                    Edit
                </button>
                <form action="{{ route('addpegawai.destroy', $user->email) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Delete</button>
                </form>
            </td>
        </tr>

        <!-- Read Modal -->
        <div class="modal fade" id="ReadModal{{ $user->id }}" tabindex="-1" aria-labelledby="ReadModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Pegawai <strong>{{$user->name}}</strong></h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Nama :</strong> {{$user->name}} </p>
                        <p><strong>Email :</strong> {{$user->email}} </p>
                        <p><strong>Role :</strong> </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="EditModal{{ $user->id }}" tabindex="-1" aria-labelledby="EditModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Pegawai <strong>{{$user->name}}</strong></h5>
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
                                    <input type="text" class="form-control" id="name" name="name" value="{{$user->name}}">
                                </div>

                                <!-- Email -->
                                <div class="form-group col-md-6">
                                    <label for="email">Email Pegawai</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{$user->email}}">
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
        @endforeach
    </tbody>
</table>
@endsection