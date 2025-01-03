@extends('layouts.tabler')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pilih Outlet</h1>
    </div>

    <div class="row">
        @foreach ($apiOutlet as $outlet)
        <div class="col-md-3 mb-3">
            <a href="{{ route('manager.cekgaji', $outlet['id']) }}" class="text-decoration-none text-dark">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $outletMapping[$outlet['id']] ?? 'Outlet Name Not Found' }}</h5>
                    </div>
                    <img src="{{ $outlet['image_url'] }}" class="card-img-top" alt="Outlet Image" style="height: auto; width: auto; object-fit: cover;">
                </div>
            </a>
        </div>
        @endforeach
    </div>
@endsection

@push('myscript')
        
@endpush