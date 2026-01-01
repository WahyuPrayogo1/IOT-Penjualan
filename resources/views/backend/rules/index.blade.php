<x-app-layout>
<div class="row">
   <div class="col-sm-12">
      <div class="card">
         <div class="card-header d-flex justify-content-between">
            <div class="header-title">
               <h4 class="card-title">Rules</h4>
            </div>
            <a href="{{ route('rules.create') }}" class="btn btn-primary">
               Add Rule
            </a>
         </div>

         <div class="card-body">
            <div class="table-responsive">
               <table id="datatable" class="table table-striped">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Status</th>
                        <th>Action</th>
                     </tr>
                  </thead>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

@push('scripts')
<script>
$(function() {
    $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('rules.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'type', name: 'type' },
            { data: 'value', name: 'value' },
            { data: 'status', name: 'status', orderable:false, searchable:false },
            { data: 'action', name: 'action', orderable:false, searchable:false }
        ]
    });
});
</script>
@endpush
</x-app-layout>
