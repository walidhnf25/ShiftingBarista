@extends('layouts.tabler')

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h2 mb-0 text-gray-800">Reservasi Shift Anda</h1>
</div>
<p class="h6 mb-3">Mohon perhatikan shift yang anda pilih dengan seksama!!</p>

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

<h2 class="h4 mb-3">PILIHAN JADWAL SHIFT</h2>

<ul class="nav nav-tabs" id="outletTabs" role="tablist">
  @foreach ($outletMapping as $id => $outletName)
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $id }}" data-bs-toggle="tab" href="#content-{{ $id }}" role="tab">
        {{ $outletName }}
      </a>
    </li>
  @endforeach
</ul>

<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="tab-content" id="outletTabsContent">
        @foreach ($outletMapping as $id => $outletName)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="content-{{ $id }}" role="tabpanel">
            <div class="table-responsive">
                <table class="table" id="outletTable-{{ $id }}">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jam Kerja</th>
                        <th>Pekerjaan</th>
                        <th>Hari</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jadwal_shift->where('id_outlet', $id) as $shift)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $shift->jamShift->jam_mulai ?? 'N/A' }} - {{ $shift->jamShift->jam_selesai ?? 'N/A' }}</td>
                        <td>{{ $shift->tipePekerjaan->tipe_pekerjaan ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($shift->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                        <td>{{ $shift->tanggal }}</td>
                        <td><a href="{{ route('getJadwalShift', $shift->id) }}" class="btn btn-primary">+</a></td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            </div>
        @endforeach
        </div>
    </div>
</div>

<div class="row"> 
    <div class="col-md-12 col-lg-12 d-flex flex-column mt-3">
        <h2 class="h4 mb-2">RESERVASI JADWAL SHIFT</h2>
        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jam Kerja</th>
                        <th>Pekerjaan</th>
                        <th>Hari</th>
                        <th>Tanggal</th>
                        <th>Outlet</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="selectedShiftData">
                    @if ($availRegister === 'No' && $kesediaanShifts->isNotEmpty())
                        @foreach ($kesediaanShifts as $shift)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $shift->jamShift ? $shift->jamShift->jam_mulai . ' - ' . $shift->jamShift->jam_selesai : 'N/A' }}
                                </td>
                                <td>{{ $shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($shift->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                                <td>{{ $shift->tanggal }}</td>
                                <td>{{ $outletMapping[$shift->id_outlet] ?? 'Outlet Not Found' }}</td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger remove-from-cache" data-id="{{ $shift->id }}" disabled>-</button>
                                </td>
                            </tr>
                        @endforeach
                    @elseif ($availRegister !== 'No' && count($cachedJadwalShifts) > 0)
                        @foreach ($cachedJadwalShifts as $shift)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $shift->jamShift ? $shift->jamShift->jam_mulai . ' - ' . $shift->jamShift->jam_selesai : 'N/A' }}
                                </td>
                                <td>{{ $shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($shift->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                                <td>{{ $shift->tanggal }}</td>
                                <td>{{ $outletMapping[$shift->id_outlet] ?? 'Outlet Not Found' }}</td>
                                <td>
                                    <form action="{{ route('removeFromCache', $shift->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger remove-from-cache">-</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="placeholder-row">
                            <td colspan="7" class="text-center">Belum ada jadwal shift dipilih</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <form id="registrationForm" action="{{ route('kesediaan.store') }}" method="POST">
                @csrf
                <button type="button" class="btn btn-primary" id="registerButton" @if($availRegister === 'No') disabled @endif>Registrasi</button>
            </form>
        </div>
    </div>
</div>

@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function () {
    // Konfirmasi sebelum melakukan registrasi
    document.getElementById("registerButton").addEventListener("click", function () {
        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: "Data yang telah diregistrasikan tidak dapat direset!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Registrasi!',
            cancelButtonText: 'Batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika user mengonfirmasi, submit form
                document.getElementById("registrationForm").submit();
            }
        });
    });

    $('[id^="outletTable-"]').each(function() {
        $(this).DataTable({
            paging: false,
            searching: false,
            order: [[0, 'asc']],
            columnDefs: [
                { targets: [1, 5], orderable: false },
                { targets: '_all', orderable: true }
            ]
        });
    });

    document.getElementById("id_outlet").addEventListener("change", function () {
        // Reset filter tipe pekerjaan saat outlet berubah
        document.getElementById("id_tipe_pekerjaan").selectedIndex = 0;

        // Submit form
        document.getElementById("filterForm").submit();
    });

    document.getElementById("id_tipe_pekerjaan").addEventListener("change", function () {
        // Submit form saat tipe pekerjaan dipilih
        document.getElementById("filterForm").submit();
    });

    // Sembunyikan alert setelah beberapa detik
    setTimeout(function () {
        $('.alert').fadeOut('slow');
    }, 2000);
});
</script>