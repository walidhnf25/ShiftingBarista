@extends('layouts.tabler')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h2 mb-0 text-gray-800">Dashboard</h1>
</div>

<div class="row">
    <!-- Today Activity Section -->
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h4 class="card-title">Jam Hari ini</h4>
                <div class="text-center">
                    <h6 id="current-time" class="font-weight-bold" style="font-size: 24px;"></h6>
                    <span class="text-muted" id="current-date"></span>
                    <br>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Shift Jam Sekarang Section -->
    <div class="col-lg-6 d-flex">
        <div class="card shadow mb-4 flex-fill">
            <div class="card-body">
                <h4 class="card-title font-weight-bold">Shift Anda Sekarang</h4>
                <div class="rounded border py-2 px-4">
                    <p class="m-0 text-danger font-weight-bold " style="font-size: 18px">Harmony Caffe</p>
                    <p class="m-0 ">Wed, 11th Mar 2019 10:00 AM</p>
                </div>
                <div class="d-flex justify-content-between my-4 w-100">
                    <div class="w-100">
                        <h6 class="text-center my-4 font-weight-bold" style="font-size: 21px">3,45 Jam Tersisa</h6>
                        <div class="progress w-100" style="height: 20px">
                            <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 40%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>  
            </div>
            <div class="d-flex justify-content-around border-top px-2 " >
                <p class="mb-0 py-4 " style="height: 100%">
                    <strong>Waktu Mulai</strong>: 08:00
                </p>
                <div class="border"></div>
                <p class="mb-0 py-4">
                    <strong>Waktu Berakhir</strong>: 14:30
                </p>
            </div>
        </div>
    </div>

    <!-- Jadwal Shift hari ini Section -->
    <div class="col-lg-6 d-flex">
        <div class="card shadow mb-4 flex-fill">
            <div class="card-body">
                <h4 class="card-title">Jadwal Shift Anda Hari ini</h4>
                <div class="d-flex flex-column" style="gap:16px">
                    <div class="mb-3 border rounded p-3">
                        <p class="font-weight-bold">Harmony Cafe</p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex flex-column lg:flex-row justify-content-between my-3">
                            <span > <strong>Status Pekerjaan :</strong> Dalam Pengerjaan</span>
                            <span > <strong>Jadwal :</strong> 08:30 - 14:00</span>
                            <span > <strong>Pekerjaan :</strong> Barista</span>
                        </div>
                    </div>
                    <div class="mb-3 border rounded p-3">
                        <p class="font-weight-bold">Literasi Cafe</p>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex flex-column lg:flex-row justify-content-between my-3">
                            <span > <strong>Status Pekerjaan :</strong> Belum Pengerjaan</span>
                            <span > <strong>Jadwal :</strong> 14:30 - 20:30</span>
                            <span > <strong>Pekerjaan :</strong> Kitchen</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    function updateTime() {
        const now = new Date();
        const currentTime = now.toLocaleTimeString(); // format hh:mm:ss
        const currentDate = now.toLocaleDateString('en-GB', { year: 'numeric', month: 'long', day: 'numeric' }); // Date in format "2nd August 2023"
        
        // Get the current hour (0-23)
        const currentHour = now.getHours();

        // kondisi icon berubah
        let icon = '';
        if (currentHour >= 6 && currentHour < 18) {
            icon = '<i class="fas fa-sun"></i>'; // Sun icon for daytime
        } else {
            icon = '<i class="fas fa-moon"></i>'; // Moon icon for night-time
        }

        // Update the time, date, and icon elements
        document.getElementById('current-time').innerHTML = icon + ' ' + currentTime;
        document.getElementById('current-date').textContent = `Hari ini: ${currentDate}`;
    }

    updateTime();

    // update detik real time
    setInterval(updateTime, 1000);
</script>
@endpush
