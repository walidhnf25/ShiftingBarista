<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
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
            {{-- <td scope="col">#</td>
            <td scope="col">Name</td>
            <td scope="col">Email</td>
            <td scope="col">Role</td>
            <td scope="col">Aksi</td> --}}
        @foreach($users as $user)
            <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>role</td>
                <td>Aksi</td>
                {{-- <td>
                    <!-- Tombol untuk Read, Update, Delete -->
                    <a href="{{ route('pegawai.show', $user->id) }}" class="btn btn-info btn-sm">Read</a>
                    <a href="{{ route('pegawai.edit', $user->id) }}" class="btn btn-warning btn-sm">Update</a>
                    <form action="{{ route('pegawai.destroy', $user->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pegawai ini?')">Delete</button>
                    </form>
                </td> --}}
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>