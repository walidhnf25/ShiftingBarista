@extends('layouts.tabler')

@section('content')

  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h2 mb-0 text-gray-800">Terapkan Shift Anda</h1>
  </div>
  <p class="h6 mb-4">Mohon Perhatikan shift yang anda pilih dengan seksama !!</p>

  <div class="row mt-5">
    <div class="col-md-12 col-lg-6">
        <h2 class="h4 mb-4">Pilihan Jadwal Shift</h2>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Outlet</th>
                    <th>Jam Kerja</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Harmony Caffe</td>
                    <td>08:00 - 14:00</td>
                    <td>13/08/2024</td>
                    <td>
                        <button type="submit" class="btn btn-outline-primary">+</button>
                    </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Harmony Caffe</td>
                    <td>14:30 - 20:00</td>
                    <td>13/08/2024</td>
                    <td>
                        <button type="submit" class="btn btn-outline-primary">+</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>LakeSide FIT</td>
                    <td>08:00 - 16:00</td>
                    <td>12/08/2024</td>
                    <td>
                        <button type="submit" class="btn btn-outline-primary ">+</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-12 col-lg-6 d-flex flex-column ">
        <h2 class="h4 mb-4">Jadwal Shift yang Anda Pilih</h2>
        <table class="table table-bordered  table-striped">
            <thead>
                <tr>
                    <td>#</td>
                    <th>Outlet</th>
                    <th>Jam Kerja</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
                
            </thead>
            <tbody>
                <tr>
                    {{-- <td>
                        <td colspan="5" class="text-center">Anda belum memilih tanda shift</td>
                    </td> --}}
                </tr>
                <tr>
                    <td>1</td>
                    <td>Harmony Caffe</td>
                    <td>12:00 - 16:00</td>
                    <td>13/08/2024</td>
                    <td>
                        <button type="submit" class="btn btn-outline-danger">-</button>
                     </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Harmony Caffe</td>
                    <td>12:00 - 16:00</td>
                    <td>13/08/2024</td>
                    <td>
                        <button type="submit" class="btn btn-outline-danger">-</button>
                     </td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Siap Acc</button>
        </div>
    </div>
</div>

@endsection