<x-app-layout>
<div class="row">
   <div class="col-sm-8 mx-auto">
      <div class="card">
         <div class="card-header">
            <h4 class="card-title">{{ $isEdit ? 'Edit Product' : 'Add Product' }}</h4>
         </div>

         <div class="card-body">
            <form action="{{ $isEdit ? route('products.update', $product->id) : route('products.store') }}" 
                  method="POST" enctype="multipart/form-data">

               @csrf
               @if($isEdit)
                  @method('PUT')
               @endif

               <div class="mb-3">
                   <label>Barcode</label>
                   <input type="text" name="barcode" class="form-control" value="{{ $product->barcode ?? old('barcode') }}" required>
               </div>

               <div class="mb-3">
                   <label>Name</label>
                   <input type="text" name="name" class="form-control" value="{{ $product->name ?? old('name') }}" required>
               </div>

               <div class="mb-3">
                   <label>Price</label>
                   <input type="number" name="price" class="form-control" value="{{ $product->price ?? old('price') }}" required>
               </div>

               <div class="mb-3">
                   <label>Stock</label>
                   <input type="number" name="stock" class="form-control" value="{{ $product->stock ?? old('stock') }}" required>
               </div>

               <div class="mb-3">
                   <label>Image (optional)</label>
                   <input type="file" name="image" class="form-control">
               </div>

               @if($isEdit && $product->image)
                  <img src="{{ asset('storage/' . $product->image) }}" width="120" class="mt-2 rounded">
               @endif

               <button class="btn btn-primary mt-3">{{ $isEdit ? 'Update' : 'Save' }}</button>

            </form>
         </div>
      </div>
   </div>
</div>
</x-app-layout>
