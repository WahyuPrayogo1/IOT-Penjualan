<x-app-layout>
<div class="row">
   <div class="col-sm-8 mx-auto">
      <div class="card">
         <div class="card-header">
            <h4 class="card-title">System Configuration</h4>
         </div>

         <div class="card-body">
            <form action="{{ route('configs.update', $config->id) }}" method="POST">
               @csrf
               @method('PUT')

               {{-- WARNING THRESHOLD --}}
               <div class="mb-3">
                   <label>Warning Threshold</label>
                   <input type="number"
                          name="warning_threshold"
                          class="form-control"
                          value="{{ old('warning_threshold', $config->warning_threshold) }}"
                          min="0" max="100" required>
                   <small class="text-muted">
                       Score minimal untuk menampilkan peringatan
                   </small>
               </div>

               {{-- BLOCK THRESHOLD --}}
               <div class="mb-3">
                   <label>Block Threshold</label>
                   <input type="number"
                          name="block_threshold"
                          class="form-control"
                          value="{{ old('block_threshold', $config->block_threshold) }}"
                          min="0" max="100" required>
                   <small class="text-muted">
                       Score minimal untuk memblokir halaman
                   </small>
               </div>

               {{-- TTL --}}
               <div class="mb-3">
                   <label>TTL (Hours)</label>
                   <input type="number"
                          name="ttl_hours"
                          class="form-control"
                          value="{{ old('ttl_hours', $config->ttl_hours) }}"
                          min="1" max="168" required>
                   <small class="text-muted">
                       Interval extension untuk cek update aturan
                   </small>
               </div>

               {{-- VERSION (READ ONLY) --}}
               <div class="mb-3">
                   <label>Current Version</label>
                   <input type="text"
                          class="form-control"
                          value="{{ $config->version }}"
                          readonly>
               </div>

               <button class="btn btn-primary mt-3">
                   Update Configuration
               </button>
            </form>
         </div>
      </div>
   </div>
</div>
</x-app-layout>
