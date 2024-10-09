<form action="{{ route('tipepekerjaan.update', $tipe_pekerjaan->id) }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <input type="text" class="form-control" id="tipe_pekerjaan" name="tipe_pekerjaan" placeholder="Tipe Pekerjaan" value="{{ old('tipe_pekerjaan', $tipe_pekerjaan->tipe_pekerjaan) }}">
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