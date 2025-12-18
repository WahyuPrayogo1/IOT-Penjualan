<x-app-layout>
<div class="row">
   <div class="col-sm-12">
      <div class="card">
         <div class="card-header d-flex justify-content-between">
            <div class="header-title">
               <h4 class="card-title">Products</h4>
            </div>
            <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
         </div>

         <div class="card-body">
            <div class="table-responsive">
               <table id="datatable" class="table table-striped">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Barcode</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
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
        ajax: "{{ route('products.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'barcode', name: 'barcode' },
            { data: 'name', name: 'name' },
            { data: 'price', name: 'price' },
            { data: 'stock', name: 'stock' },
            { data: 'action', name: 'action', orderable:false, searchable:false }
        ]
    });
});
</script>
@endpush
</x-app-layout>
