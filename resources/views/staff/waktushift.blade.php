@extends('layouts.tabler')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h2 mb-2 text-gray-800">Jadwal Shift Anda</h1>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-body ">
                <div id="calendar"></div>
                <div class="mt-4 d-flex flex-column" style="gap:8px; width:12rem;">         
                    <p class="mb-0 d-flex ">Keterangan Warna :</p>
                    <li class="btn text-white" style="background-color:red;">Harmony Cafe</li>
                    <li class="btn text-white" style="background-color: #1cc88a">Literasi Cafe</li>
                    <li class="btn text-white" style="background-color: #4e73df">Lakeside</li>
                    <li class="btn text-white" style="background-color: #f6c23e">Lakeside FIT+</li>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    /* Mengubah warna background dan cursor saat hover pada tanggal di kalender */
    .fc-daygrid-day:hover {
        background-color: #e0e0e0 !important; /* Warna abu-abu */
        cursor: pointer; /* Ubah cursor menjadi pointer */
    }
</style>
@endpush

@push('myscript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            height: 800,
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: [
                { 
                    title: 'Lakeside',
                    start: '2024-11-10',
                    backgroundColor: '#4e73df'
                },
                { 
                    title: 'Shift Pagi',
                    start: '2024-11-10',
                    backgroundColor: '#4e73df'
                },
                { 
                    title: 'Shift Siang',
                    title: 'Literasi Cafe',
                    start: '2024-11-12',
                    backgroundColor: '#1cc88a'
                },
                { 
                    title: 'Lakeside FIT+',
                    start: '2024-11-15',
                    backgroundColor: '#f6c23e'
                }
            ],
            dateClick: function(info) {
                // Filter events that match the clicked date
                var events = calendar.getEvents().filter(event => event.startStr === info.dateStr);

                if (events.length > 0) {
                    // If there are events, display them in SweetAlert
                    var eventTitles = events.map(event => event.title).join(', ');
                    Swal.fire({
                        title: `Shifts on ${info.dateStr}`,
                        text: `Shift(s): ${eventTitles}`,
                        icon: 'info'
                    });
                } else {
                    // If there are no events, show a message
                    Swal.fire({
                        title: `No Shifts`,
                        text: `No shifts scheduled for ${info.dateStr}`,
                        icon: 'warning'
                    });
                }
            }
        });
        calendar.render();
    });
</script>

@endpush