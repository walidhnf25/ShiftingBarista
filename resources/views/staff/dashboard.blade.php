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
                @if ($validShifts->isEmpty())
                    <div class="d-flex flex-column align-items-center justify-content-center rounded border py-2 px-4 mb-2 text-center">
                        <p class="m-0 text-danger font-weight-bold" style="font-size: 18px">
                            <lottie-player src="https://lottie.host/488cb13b-82a7-4281-83bd-107bf1dda38c/CT1oRQv7ok.json" background="##FFFFFF" speed="1" style="width: 100px; height: 100px" loop autoplay direction="1" mode="normal"></lottie-player>
                        </p>
                        <p class="m-0 text-danger font-weight-bold" style="font-size: 18px">
                            Jadwal Shift sekarang tidak ada
                        </p>
                    </div>
                @else
                    @foreach ($validShifts as $shift)
                        <div class="rounded border py-2 px-4 mb-2">
                            <p class="m-0 text-secondary font-weight-bold text-center" style="font-size: 18px">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="30"  height="30"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-coffee"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 14c.83 .642 2.077 1.017 3.5 1c1.423 .017 2.67 -.358 3.5 -1c.83 -.642 2.077 -1.017 3.5 -1c1.423 -.017 2.67 .358 3.5 1" /><path d="M8 3a2.4 2.4 0 0 0 -1 2a2.4 2.4 0 0 0 1 2" /><path d="M12 3a2.4 2.4 0 0 0 -1 2a2.4 2.4 0 0 0 1 2" /><path d="M3 10h14v5a6 6 0 0 1 -6 6h-2a6 6 0 0 1 -6 -6v-5z" /><path d="M16.746 16.726a3 3 0 1 0 .252 -5.555" /></svg>
                            </p>
                            <p class="m-0 text-dark font-weight-bold text-center" style="font-size: 18px">
                                {{ $outletMapping[$shift->id_outlet] ?? 'Unknown Outlet' }}
                            </p>
                            <p class="m-0 text-center">
                            {{ $shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A' }}: {{ $shift->jamShift ? $shift->jamShift->jam_mulai . ' - ' . $shift->jamShift->jam_selesai : 'N/A' }}
                            </p>
                        </div>
                    @endforeach
                @endif
                @php
                    // Menemukan shift pertama yang valid
                    $shiftValid = $allShiftsToday->first(function ($shift) {
                        return $shift->status == 'Dalam Pengerjaan' || $shift->status == 'Akan Segera Dimulai';
                    });
                @endphp

                @if ($shiftValid)
                    <div class="d-flex justify-content-between my-4 w-100">
                        <div class="w-100">
                            <!-- Remaining Time -->
                            <h6 class="text-center my-4 font-weight-bold" style="font-size: 21px">
                                @if ($shiftValid->status == 'Dalam Pengerjaan')
                                    @if ($shiftValid->remaining_hours < 1)
                                        {{ round($shiftValid->remaining_seconds / 60) }} Menit Tersisa
                                    @else
                                        {{ $shiftValid->remaining_hours }} Jam Tersisa
                                    @endif
                                @elseif ($shiftValid->status == 'Sudah Berakhir')
                                    <span class="text-success">Sudah Berakhir</span>
                                @else
                                    Tidak Ada Shift yang sedang Berjalan
                                @endif
                            </h6>

                            <!-- Progress Bar -->
                            <div class="progress w-100" style="height: 20px">
                                <div class="progress-bar progress-bar-striped 
                                    {{ $shiftValid->status == 'Dalam Pengerjaan' ? 'bg-success' : 'bg-danger' }}" 
                                    role="progressbar" 
                                    style="width: {{ $shiftValid->progress }}%" 
                                    aria-valuenow="{{ $shiftValid->progress }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="d-flex justify-content-between my-4 w-100">
                        <div class="w-100">
                            <h6 class="text-center my-4 font-weight-bold" style="font-size: 21px">
                                Tidak Ada Shift yang sedang Berjalan
                            </h6>
                            <div class="progress w-100" style="height: 20px">
                                <div class="progress-bar progress-bar-striped bg-danger" 
                                    role="progressbar" 
                                    style="width: 0%" 
                                    aria-valuenow="0" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                </div>
            @if ($validShifts->isEmpty())
            <div class="d-flex justify-content-around border-top px-2 " >
                <p class="mb-0 py-4 " style="height: 100%">
                    <strong>Waktu Mulai</strong>: -
                </p>
                <div class="border"></div>
                <p class="mb-0 py-4">
                    <strong>Waktu Berakhir</strong>: -
                </p>
            </div>
            @else
            @foreach ($validShifts as $shift)
            <div class="d-flex justify-content-around border-top px-2 " >
                <p class="mb-0 py-4 " style="height: 100%">
                    <strong>Waktu Mulai</strong>: {{ $shift->jamShift ? $shift->jamShift->jam_mulai : 'N/A' }}
                </p>
                <div class="border"></div>
                <p class="mb-0 py-4">
                    <strong>Waktu Berakhir</strong>: {{ $shift->jamShift ? $shift->jamShift->jam_selesai : 'N/A' }}
                </p>
            </div>
            @endforeach
            @endif
        </div>
    </div>

    <!-- Jadwal Shift hari ini Section -->
    <div class="col-lg-6 d-flex">
    <div class="card shadow mb-4 flex-fill">
        <div class="card-body">
            <h4 class="card-title">Jadwal Shift Anda Hari ini</h4>
            <div class="d-flex flex-column" style="gap:16px">
                @foreach ($allShiftsToday as $shift)
                    @php
                        $currentTime = now('Asia/Jakarta');
                        $startTime = $shift->jamShift ? \Carbon\Carbon::parse($shift->jamShift->jam_mulai, 'Asia/Jakarta') : null;
                        $endTime = $shift->jamShift ? \Carbon\Carbon::parse($shift->jamShift->jam_selesai, 'Asia/Jakarta') : null;

                        // Determine the status based on time
                        $status = 'Sudah Berakhir';
                        if ($startTime && $endTime) {
                            if ($currentTime->between($startTime, $endTime)) {
                                $status = 'Dalam Pengerjaan';
                            } elseif ($currentTime < $startTime) {
                                $status = 'Akan Segera Dimulai';
                            }
                        }

                        // Define dynamic card classes
                        $cardClass = $status === 'Sudah Berakhir' ? 'bg-secondary text-white' : 'bg-white text-dark';
                    @endphp

                    <div class="mb-3 border rounded p-3 {{ $cardClass }}">
                        <p class="font-weight-bold">{{ $outletMapping[$shift->id_outlet] ?? 'Unknown Outlet' }}</p>
                        
                        <!-- Progress Bar -->
                        <div class="progress" style="height: 10px;">
                        @php
                                    $progress = 0; // Default progress
                                    if ($shift->jamShift) {
                                        $currentTime = now('Asia/Jakarta');
                                        $startTime = \Carbon\Carbon::parse($shift->jamShift->jam_mulai, 'Asia/Jakarta');
                                        $endTime = \Carbon\Carbon::parse($shift->jamShift->jam_selesai, 'Asia/Jakarta');

                                        if ($currentTime >= $startTime && $currentTime <= $endTime) {
                                            // Calculate elapsed time percentage
                                            $totalTime = $endTime->diffInSeconds($startTime);
                                            $elapsedTime = $currentTime->diffInSeconds($startTime);
                                            $progress = ($elapsedTime / $totalTime) * 100;
                                        }
                                    }
                                @endphp

                            <div class="progress-bar bg-success" 
                                style="width: {{ $progress }}%;" 
                                aria-valuenow="{{ $progress }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                            </div>
                        </div>

                            <!-- Shift Details -->
                            <div class="d-flex flex-column lg:flex-row justify-content-between my-3">
                                <!-- Status Pekerjaan -->
                                @php
                                    $currentTime = now('Asia/Jakarta');
                                    $startTime = $shift->jamShift ? \Carbon\Carbon::parse($shift->jamShift->jam_mulai, 'Asia/Jakarta') : null;
                                    $endTime = $shift->jamShift ? \Carbon\Carbon::parse($shift->jamShift->jam_selesai, 'Asia/Jakarta') : null;

                                    // Determine the status based on time
                                    $status = 'Sudah Berakhir';
                                    if ($startTime && $endTime) {
                                        if ($currentTime->between($startTime, $endTime)) {
                                            $status = 'Dalam Pengerjaan';
                                        } elseif ($currentTime < $startTime) {
                                            $status = 'Akan Segera Dimulai';
                                        }
                                    }
                                @endphp

                                <span><strong>Status Pekerjaan:</strong> {{ $status }}</span>
                                
                                <!-- Jadwal -->
                                <span><strong>Jadwal:</strong> 
                                    {{ $shift->jamShift ? $shift->jamShift->jam_mulai . ' - ' . $shift->jamShift->jam_selesai : 'N/A' }}
                                </span>
                                
                                <!-- Pekerjaan -->
                                <span><strong>Pekerjaan:</strong> 
                                    {{ $shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                    @if ($allShiftsToday->isEmpty())
                    <div class="d-flex flex-column align-items-center justify-content-center mb-3" style="min-height: 300px;"> 
                        <p class="font-weight-bold"><lottie-player src="https://lottie.host/b131c2ac-8b3b-40d5-90a7-a4072bf7dd59/2lzD12702I.json" background="##fff" speed="1" style="width: 100px; height: 100px" loop autoplay direction="1" mode="normal"></lottie-player></p>
                        <p class="m-0 text-danger font-weight-bold" style="font-size: 18px">
                            Jadwal Shift hari ini tidak ada
                        </p>
                    </div>
                @endif
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
