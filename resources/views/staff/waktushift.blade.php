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
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
</style>
@endpush

@push('myscript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            width:100,
            height:650,
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: [
                { 
                    title: 'Shift Pagi',
                    start: '2024-11-10',
                    backgroundColor: '#4e73df'
                },
                { 
                    title: 'Shift Siang',
                    start: '2024-11-12',
                    backgroundColor: '#1cc88a'
                },
                { 
                    title: 'Shift Malam',
                    start: '2024-11-15',
                    backgroundColor: '#f6c23e'
                }
            ]
        });
        calendar.render();
    });
</script>
@endpush