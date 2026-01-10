<x-app-layout>
    <div class="row">
        <div class="col-sm-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $isEdit ? 'Edit Device' : 'Add New Device' }}</h4>
                </div>

                <div class="card-body">
                    <form action="{{ $isEdit ? route('devices.update', $device->id) : route('devices.store') }}" 
                          method="POST">
                        @csrf
                        @if($isEdit)
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Device ID</label>
                            <input type="text" 
                                   name="device_id" 
                                   class="form-control @error('device_id') is-invalid @enderror"
                                   value="{{ old('device_id', $device->device_id ?? '') }}"
                                   placeholder="Example: IOT-01, IOT-02"
                                   required>
                            @error('device_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Unique ID for the device. Will be used in API URL</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ ($device->status ?? '') == 'active' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="inactive" {{ ($device->status ?? '') == 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" 
                                      class="form-control" 
                                      rows="3"
                                      placeholder="Location: Kasir 1, Keranjang A, etc.">{{ old('notes', $device->notes ?? '') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('devices.index') }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">
                                {{ $isEdit ? 'Update Device' : 'Save Device' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>