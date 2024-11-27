<form action="{{ route('jamShift.update', $jamShift->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label for="jam_mulai">Jam Mulai</label>
                <input type="time" class="form-control" id="jamMulai" name="jam_mulai" 
                    value="{{ $jamShift->jam_mulai }}" placeholder="Jam Mulai">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label for="jam_selesai">Jam Selesai</label>
                <input type="time" class="form-control" id="jamSelesai" name="jam_selesai" 
                    value="{{ $jamShift->jam_selesai }}" placeholder="Jam Selesai">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label for="id_outlet">Outlet</label>
                <select class="form-control" id="id_outlet" name="id_outlet" required>
                    <option value="" disabled selected>Pilih Outlet</option>
                    @foreach ($apiOutlet as $item)
                        <option value="{{ $item['id'] }}" 
                            {{ $jamShift->id_outlet == $item['id'] ? 'selected' : '' }}>
                            {{ $item['outlet_name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group d-flex justify-content-center">
                <button type="submit" class="btn btn-primary flex-grow-1">Update</button>
            </div>
        </div>
    </div>
</form>
