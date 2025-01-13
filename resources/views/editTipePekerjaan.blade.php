<form action="{{ route('tipepekerjaan.update', $tipe_pekerjaan->id) }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label for="tipe_pekerjaan">Tipe Pekerjaan</label>
                <input type="text" class="form-control" id="tipe_pekerjaan" name="tipe_pekerjaan" placeholder="Tipe Pekerjaan" value="{{ old('tipe_pekerjaan', $tipe_pekerjaan->tipe_pekerjaan) }}">
            </div>
            <div class="col-12 my-2">
                <strong class="h5 font-weight-bold text-dark"> Fee Pegawai </strong>
            </div>
            <div class="col-12 mx-0">
                <div class="form-group mb-3"> <!-- Corrected class name -->
                    <label for="min_fee">Minimum Fee</label>
                    <input type="number" class="form-control" id="min_fee" name="min_fee" placeholder="Minimum Fee Pegawai" value="{{ old('tipe_pekerjaan', $tipe_pekerjaan->min_fee) }}">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group mb-3"> <!-- Corrected class name -->
                    <label for="avg_fee">Average Fee</label>
                    <input type="number" class="form-control" id="avg_fee" name="avg_fee" placeholder="Average Fee Pegawai" value="{{ old('tipe_pekerjaan', $tipe_pekerjaan->avg_fee) }}">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group mb-3"> <!-- Corrected class name -->
                    <label for="max_fee">Maximum Fee</label>
                    <input type="number" class="form-control" id="max_fee" name="max_fee" placeholder="Maximum Fee Pegawai" value="{{ old('tipe_pekerjaan', $tipe_pekerjaan->max_fee) }}">
                </div>
            </div>
            <div class="col-12 my-2">
                <strong class="h5 font-weight-bold text-dark"> Pendapatan Outlet </strong>
            </div>
            <div class="col-12">
                <div class="form-group mb-3"> 
                    <label for="pendapatan_batas_atas">Batas Atas</label>
                    <input type="number" class="form-control" id="pendapatan_batas_atas" name="pendapatan_batas_atas" placeholder="Batas Atas Penghasilan" value="{{ old('tipe_pekerjaan', $tipe_pekerjaan->pendapatan_batas_atas) }}">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group mb-3"> <!-- Corrected class name -->
                    <label for="pendapatan_batas_bawah">Batas Bawah</label>
                    <input type="number" class="form-control" id="pendapatan_batas_bawah" name="pendapatan_batas_bawah" placeholder="Batas Bawah Penghasilan" value="{{ old('tipe_pekerjaan', $tipe_pekerjaan->pendapatan_batas_bawah) }}">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group d-flex justify-content-center">
                <button type="submit" class="btn btn-primary flex-grow-1">Simpan</button>
            </div>
        </div>
    </div>
</form>