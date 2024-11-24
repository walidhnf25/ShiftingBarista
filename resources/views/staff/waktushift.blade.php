@extends('layouts.tabler')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between">
    <h1 class="h2 text-gray-800">Jadwal Shift Anda</h1>
</div>

<div class="row">
    <div class="col mb-2">
        <i>Keterangan:</i>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <button class="btn text-white w-100" style="background-color:red;" disabled>Harmony Cafe</button>
    </div>
    <div class="col-md-3">
        <button class="btn text-white w-100" style="background-color: #1cc88a;" disabled>Literasi Cafe</button>
    </div>
    <div class="col-md-3">
        <button class="btn text-white w-100" style="background-color: #4e73df;" disabled>Lakeside</button>
    </div>
    <div class="col-md-3">
        <button class="btn text-white w-100" style="background-color: #f6c23e;" disabled>Lakeside FIT+</button>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mt-3">
        <div id="calendar"></div>
    </div>
</div>


@endsection

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
            right: 'dayGridMonth'
        },
        events: [
            @foreach ($jadwal_shifts as $shift)
                {
                    title:'{{ $shift->jam_kerja }}',
                    start: '{{ $shift->tanggal }}',
                    backgroundColor: getOutletColor('{{ $shift->id_outlet }}'),
                    extendedProps: {
                        outletName: '{{ $outletMapping[$shift->id_outlet] ?? 'Unknown Outlet' }}',
                        jamKerja: '{{ $shift->jam_kerja }}',
                        idOutlet: '{{ $shift->id_outlet }}'
                    }
                },
            @endforeach
        ],

        // Event click handler
        eventClick: function(info) {
            // Retrieve event data
            var event = info.event;
            var outletName = event.extendedProps.outletName || 'Unknown Outlet';
            var jamKerja = event.extendedProps.jamKerja || 'N/A';

            // Show Swal.fire with details
            Swal.fire({
                title: `Detail Jadwal`,
                html: `<strong>Outlet:</strong> ${outletName}<br><strong>Jam Kerja:</strong> ${jamKerja}`,
                icon: 'info'
            });
        },

        dateClick: function(info) {
            // Get events for the clicked date
            var events = calendar.getEvents().filter(event => event.startStr === info.dateStr);

            if (events.length > 0) {
                // Filter events to include only those with valid id_outlet
                var validEvents = events.filter(event => event.extendedProps.idOutlet);

                if (validEvents.length > 0) {
                    // Generate details for valid events
                    var eventDetails = validEvents.map(event => {
                        return `<strong>${event.extendedProps.outletName}:</strong> ${event.extendedProps.jamKerja}`;
                    }).join('<br>');

                    // Show Swal.fire with event details
                    Swal.fire({
                        title: `Detail Shift Tanggal ${info.dateStr}`,
                        html: eventDetails,
                        icon: 'info'
                    });
                } else {
                    // Do nothing if no valid events with idOutlet
                    console.log("No valid events for this date.");
                }
            } else {
                // Show a message if no events at all
                Swal.fire({
                    title: `Tidak ada jadwal shift`,
                    text: `Tidak ada jadwal shift untuk ${info.dateStr}`,
                    icon: 'info'
                });
            }
        }
    });
    calendar.render();

    // Function to map outlet IDs to specific colors
    function getOutletColor(outletId) {
        var colorMapping = {
            'OUT-I0KWK8GSNN': 'red',        // Harmony Cafe
            'OUT-AUWXFVYRPA': '#4e73df',   // Lakeside
            'OUT-GCNV7MW5YK': '#1cc88a',   // Literasi Cafe
            'OUT-UP6VLASEJX': '#f6c23e'    // Lakeside FIT+
        };
        return colorMapping[outletId] || '#000000'; // Default color if not found
    }
});

</script>
@endpush