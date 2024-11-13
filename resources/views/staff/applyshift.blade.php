@extends('layouts.tabler')

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h2 mb-0 text-gray-800">Terapkan Shift Anda</h1>
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

<div class="row"> 
    <div class="col mb-3">
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                {{ strtoupper('All Outlet') }}
            </button>
            <div class="dropdown-menu">
                <button class="dropdown-item all-outlet-button" type="button">
                    {{ strtoupper('All Outlet') }}
                </button>
                @foreach ($outletMapping as $id => $outletName)
                    <button class="dropdown-item outlet-button" type="button" data-id="{{ $id }}">
                        {{ $outletName }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-lg-12">
        <h2 class="h4 mb-2">PILIHAN JADWAL SHIFT</h2>
        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
            <table class="table table-bordered table-striped" id="shiftTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jam Kerja</th>
                        <th>Pekerjaan</th>
                        <th>Tanggal</th>
                        <th>Outlet</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="shiftData">
                    @if ($jadwal_shift->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">Jadwal Shift Kosong.</td>
                        </tr>
                    @else
                        @foreach ($jadwal_shift as $shift)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $shift->jam_kerja }}</td>
                                <td>{{ $shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A' }}</td>
                                <td>{{ $shift->tanggal }}</td>
                                <td>{{ $outletMapping[$shift->id_outlet] }}</td>
                                <td>
                                    <button type="button" class="btn btn-outline-primary add-to-cache" data-id="{{ $shift->id }}">+</button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
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
                        <th>Tanggal</th>
                        <th>Outlet</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="selectedShiftData">
                    @if(count($cachedJadwalShifts) > 0)
                        @foreach($cachedJadwalShifts as $shift)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $shift->jam_kerja }}</td>
                                <td>{{ $shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A' }}</td>
                                <td>{{ $shift->tanggal }}</td>
                                <td>{{ $outletMapping[$shift->id_outlet] }}</td>
                                <!-- Add the button to dynamically remove the shift -->
                                <td><button type="button" class="btn btn-outline-danger add-to-cache" data-id="{{ $shift->id }}">-</button></td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="placeholder-row">
                            <td colspan="6" class="text-center">Belum ada shift yang dipilih.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <form action="{{ route('kesediaan.store') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
        </div>
    </div>
</div>
@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function () {
    // Fungsi untuk menambah shift ke cache saat tombol diklik
    function rebindAddToCache() {
        $('.add-to-cache').off('click').on('click', function () {
            let shiftId = $(this).data('id');
            
            // Kirim permintaan AJAX untuk menyimpan ID shift ke cache
            $.ajax({
                url: '/getJadwalshift/' + shiftId,
                method: 'GET',
                success: function (response) {
                    // Update tabel dengan data terbaru
                    $('#cachedShifts').html(response);
                },
                error: function (xhr) {
                    console.error('Error:', xhr);
                }
            });
        });
    }

    // Binding awal untuk tombol "add to cache"
    rebindAddToCache();

    // Saat outlet dipilih
    $('.outlet-button').click(function () {
        let outletId = $(this).data('id');
        let outletName = $(this).text(); // Dapatkan nama outlet yang dipilih

        // Update teks tombol dengan nama outlet yang dipilih
        $('.btn.btn-primary.dropdown-toggle').text(outletName);

        // Ambil data shift yang difilter berdasarkan ID outlet
        $.ajax({
            url: "{{ route('filterJadwalShift') }}",
            method: "GET",
            data: { outlet_id: outletId },
            success: function (response) {
                // Masukkan data yang difilter ke dalam tabel
                $('#shiftData').html(response);

                // Re-bind tombol "add to cache" untuk data baru
                rebindAddToCache();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Failed to fetch data. ' + textStatus + ": " + errorThrown);
            }
        });
    });

    // Saat tombol "All Outlet" diklik
    $('.all-outlet-button').click(function () {
        // Reset teks tombol kembali ke "All Outlet"
        $('.btn.btn-primary.dropdown-toggle').text('ALL OUTLET');

        // Ambil semua data shift tanpa filter berdasarkan outlet
        $.ajax({
            url: "{{ route('filterJadwalShift') }}",
            method: "GET",
            data: { outlet_id: '' },
            success: function (response) {
                // Masukkan semua data ke dalam tabel
                $('#shiftData').html(response);

                // Re-bind tombol "add to cache" untuk semua data
                rebindAddToCache();
            },
            error: function () {
                alert('Failed to fetch data.');
            }
        });
    });

    $('#shiftTable').on('click', '.add-to-cache', function () {
        var shiftId = $(this).data('id'); // Dapatkan ID shift dari data-id

        // Kirim permintaan AJAX untuk menyimpan shift dalam cache dan mendapatkan data shift
        $.ajax({
            url: '/storeAndGetJadwalshift/' + shiftId, // Sesuaikan route
            method: 'GET',
            success: function (response) {
                // Iterasi melalui semua shift yang ada dalam response dan tambahkan ke tabel
                $.each(response.jadwal_shifts, function(index, shiftData) {
                    // Periksa apakah shift ini sudah ditambahkan ke tabel
                    var existingRow = $('#selectedShiftData tr[data-id="' + shiftData.no + '"]');
                    
                    // Jika shift ini belum ada di tabel, tambahkan
                    if (existingRow.length === 0) {
                        var rowHtml = `
                            <tr data-id="${shiftData.no}">
                                <td>${shiftData.no}</td>
                                <td>${shiftData.jam_kerja}</td>
                                <td>${shiftData.pekerjaan}</td>
                                <td>${shiftData.tanggal}</td>
                                <td>${shiftData.outletName}</td>
                                <td><button type="button" class="btn btn-outline-primary remove-from-cache" data-id="${shiftData.no}">-</button></td> <!-- Tombol hapus -->
                            </tr>
                        `;
                        
                        // Tambahkan baris baru ke tabel
                        $('#selectedShiftData').append(rowHtml);

                        // Hapus baris placeholder jika ada
                        $('#selectedShiftData .placeholder-row').remove();
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error('Error fetching shift data:', error);
            }
        });
    });

    $('#shiftTable').on('click', '.view-shift', function () {
        var shiftId = $(this).data('id'); // Ambil ID shift dari data-id

        // Kirim permintaan AJAX untuk mengambil data shift berdasarkan ID
        $.ajax({
            url: '/getJadwalshift/' + shiftId, // Sesuaikan dengan route Anda
            method: 'GET',
            success: function (response) {
                var shiftData = response.shift;

                // Tampilan data shift di modal atau tempat lain
                alert('Jam Kerja: ' + shiftData.jam_kerja);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching shift data:', error);
            }
        });
    });

    // Sembunyikan alert setelah beberapa detik
    setTimeout(function () {
        $('.alert').fadeOut('slow');
    }, 2000);
});
</script>