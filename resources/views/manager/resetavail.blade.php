@extends('layouts.tabler')

@section('content')
    {{-- PAGE HEADING --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reset User</h1>
    </div>

    {{-- DROPDOWN DAN TOMBOL RESET --}}

    <form action="{{ route('resetavail.store') }}" method="POST" id="resetForm">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <select id="user-select" name="user_id" class="form-control w-100" required>
                        <option value="" disabled selected>Pilih User</option>
                        @foreach ($users as $user)
                            @if ($user->avail_register === 'No')
                            <option value="{{ $user->id }}">
                                {{ strtoupper($user->name) }}{{ $user->role === 'Manager' ? ' (MANAGER)' : '' }}
                            </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-3">RESET</button>
            </div>
        </div>
    </form>

    {{-- TABLE RESET DONE --}}
    <h4 class="my-3">Daftar User Siap Registrasi</h4>

    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Status Registrasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usersForTable as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ strtoupper($user->name) }}{{ $user->role === 'Manager' ? ' (MANAGER)' : '' }}
                            </td>
                            <td>{{ $user->avail_register }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

<script>
    $(document).ready(function() {
        $('#resetForm').on('submit', function(e) {
            e.preventDefault();

            const userId = $('#user-select').val();
            if (!userId) {
                alert('Silakan pilih user terlebih dahulu');
                return;
            }

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        alert('Status berhasil direset');
                        location.reload(); // Reload halaman untuk memperbarui tabel
                    } else {
                        alert('Terjadi kesalahan: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan pada server');
                }
            });
        });
    });
</script>
