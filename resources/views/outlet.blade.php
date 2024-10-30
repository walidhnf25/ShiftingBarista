@extends('layouts.tabler')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pilih Outlet</h1>
    </div>

    <div class="row">
        @foreach ($apiOutlet as $outlet)
        <div class="col-md-3 mb-4">
            <a href="{{ route('outlet.jadwalshift', $outlet['id']) }}" class="text-decoration-none text-dark">
                <div class="card h-100">
                    <img src="{{ $outlet['image_url'] }}" class="card-img-top" alt="Outlet Image" style="height: 250px; object-fit: cover;">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $outletMapping[$outlet['id']] ?? 'Outlet Name Not Found' }}</h5>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
@endsection

@push('myscript')
        
@endpush