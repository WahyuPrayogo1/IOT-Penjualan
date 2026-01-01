<x-app-layout>
<div class="row">
   <div class="col-sm-8 mx-auto">
      <div class="card">
         <div class="card-header">
            <h4 class="card-title">
                {{ $isEdit ? 'Edit Rule' : 'Add Rule' }}
            </h4>
         </div>

         <div class="card-body">
            <form action="{{ $isEdit ? route('rules.update', $rule->id) : route('rules.store') }}" 
                  method="POST">

               @csrf
               @if($isEdit)
                  @method('PUT')
               @endif

               {{-- TYPE --}}
               <div class="mb-3">
                   <label>Rule Type</label>
                   <select name="type" class="form-control" required>
                       <option value="">-- Select Type --</option>
                       <option value="keyword" 
                           {{ old('type', $rule->type ?? '') == 'keyword' ? 'selected' : '' }}>
                           Keyword
                       </option>
                       <option value="phrase" 
                           {{ old('type', $rule->type ?? '') == 'phrase' ? 'selected' : '' }}>
                           Phrase
                       </option>
                       <option value="safe" 
                           {{ old('type', $rule->type ?? '') == 'safe' ? 'selected' : '' }}>
                           Safe Pattern
                       </option>
                   </select>
               </div>

               {{-- VALUE --}}
               <div class="mb-3">
                   <label>Rule Value</label>
                   <input type="text" name="value" class="form-control"
                          placeholder="Contoh: slot online / bahaya judi / .ac.id"
                          value="{{ old('value', $rule->value ?? '') }}"
                          required>
               </div>

               {{-- STATUS (EDIT ONLY) --}}
               @if($isEdit)
               <div class="mb-3">
                   <label>Status</label>
                   <select name="is_active" class="form-control">
                       <option value="1" {{ $rule->is_active ? 'selected' : '' }}>Aktif</option>
                       <option value="0" {{ !$rule->is_active ? 'selected' : '' }}>Nonaktif</option>
                   </select>
               </div>
               @endif

               <button class="btn btn-primary mt-3">
                   {{ $isEdit ? 'Update' : 'Save' }}
               </button>

               <a href="{{ route('rules.index') }}" class="btn btn-secondary mt-3">
                   Cancel
               </a>

            </form>
         </div>
      </div>
   </div>
</div>
</x-app-layout>
