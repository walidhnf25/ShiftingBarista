<form action="{{ route('periodegaji.update', $periode_gaji->id) }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label for="nama_periode_gaji">Nama periode</label>
                <input type="text" class="form-control" id="nama_periode_gaji" name="nama_periode_gaji" value="{{ old('nama_periode_gaji', $periode_gaji->nama_periode_gaji) }}">
            </div>
        </div>
        <div class="col-12">
            <div class="form-group mb-3">
                <label for="tgl_mulai">Tanggal Mulai</label>
                <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" value="{{ old('tgl_mulai', $periode_gaji->tgl_mulai) }}">
            </div>
        </div>
        <div class="col-12">
            <div class="form-group mb-3">
                <label for="tgl_akhir">Tanggal Akhir</label>
                <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir" value="{{ old('tgl_akhir', $periode_gaji->tgl_akhir) }}">
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