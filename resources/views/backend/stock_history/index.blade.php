<x-app-layout>

<div class="row">
   <div class="col-sm-12">
      <div class="card">
         <div class="card-header">
            <h4 class="card-title">Stock History</h4>
         </div>

         <div class="card-body">
            <div class="table-responsive">
               <table id="datatable" class="table table-striped">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Description</th>
                        <th>Date</th>
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
        ajax: "{{ route('stock-history.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'product', name: 'product' },
            { data: 'type', name: 'type' },
            { data: 'quantity', name: 'quantity' },
            { data: 'description', name: 'description' },
            { data: 'date', name: 'date' },
            { data: 'action', name: 'action', orderable:false, searchable:false },
        ]
    });
});
</script>
@endpush

</x-app-layout>
