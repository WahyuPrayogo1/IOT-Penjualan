<x-app-layout>
<div class="row">
   <div class="col-md-10 mx-auto">
      <div class="card">
         <div class="card-header d-flex justify-content-between">
            <h4>Create Sales</h4>
         </div>

         <div class="card-body">

            <form action="{{ route('sales.store') }}" method="POST">
               @csrf

               <div class="mb-3">
                  <label>Customer Name</label>
                  <input type="text" name="customer_name" class="form-control">
               </div>

               <h5>Items</h5>
               <table class="table table-bordered" id="items-table">
                  <thead>
                     <tr>
                        <th>Product</th>
                        <th width="120px">Price</th>
                        <th width="120px">Qty</th>
                        <th width="120px">Subtotal</th>
                        <th width="50px">#</th>
                     </tr>
                  </thead>
                  <tbody>
                     <!-- Row will be added by jQuery -->
                  </tbody>
               </table>

               <button type="button" class="btn btn-secondary" id="addRow">+ Add Item</button>

               <hr>

               <div class="text-end">
                   <h4>Total: <span id="total-rp">Rp 0</span></h4>
                   <input type="hidden" id="total_amount" name="total_amount">
               </div>

               <div class="mb-3 mt-3">
                  <label>Payment Method</label>
                  <select name="payment_method" class="form-control">
                     <option value="cash">Cash</option>
                     <option value="qris">QRIS</option>
                  </select>
               </div>

               <div class="mb-3">
                  <label>Paid Amount</label>
                  <input type="number" name="paid_amount" class="form-control">
               </div>

               <button class="btn btn-primary">Submit</button>
            </form>

         </div>
      </div>
   </div>
</div>

@push('scripts')
<script>
let row = 0;

// Product data (passed from controller to Blade)
let products = @json(\App\Models\Product::all());

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(angka);
}

function addRow() {
    let html = `
        <tr>
            <td>
                <select name="items[${row}][product_id]" class="form-control product-select">
                    <option value="">-- pilih produk --</option>
                    ${products.map(p => `
                        <option value="${p.id}" data-price="${p.price}">
                            ${p.name} - ${formatRupiah(p.price)}
                        </option>
                    `)}
                </select>
            </td>

            <td>
                <input type="text" class="form-control price" name="items[${row}][price]" readonly>
            </td>

            <td>
                <input type="number" class="form-control qty" name="items[${row}][quantity]" min="1" value="1">
            </td>

            <td>
                <input type="text" class="form-control subtotal" name="items[${row}][subtotal]" readonly>
            </td>

            <td>
                <button type="button" class="btn btn-sm btn-danger removeRow">X</button>
            </td>
        </tr>
    `;

    $('#items-table tbody').append(html);
    row++;
}

function updateTotals() {
    let total = 0;

    $('.subtotal').each(function() {
        let val = Number($(this).val()) || 0;
        total += val;
    });

    $('#total_amount').val(total);
    $('#total-rp').text(formatRupiah(total));
}

// Event: Tambah baris
$('#addRow').on('click', function() {
    addRow();
});

// Event: Pilih produk → auto ganti harga & subtotal
$(document).on('change', '.product-select', function() {
    let price = $(this).find(':selected').data('price') || 0;
    let qty = $(this).closest('tr').find('.qty').val();

    $(this).closest('tr').find('.price').val(price);
    $(this).closest('tr').find('.subtotal').val(price * qty);

    updateTotals();
});

// Event: Ganti qty → auto subtotal
$(document).on('input', '.qty', function() {
    let rowElement = $(this).closest('tr');
    let price = Number(rowElement.find('.price').val()) || 0;
    let qty = Number($(this).val()) || 0;

    rowElement.find('.subtotal').val(price * qty);

    updateTotals();
});

// Event: Hapus baris
$(document).on('click', '.removeRow', function() {
    $(this).closest('tr').remove();
    updateTotals();
});

// Initial row
addRow();
</script>
@endpush

</x-app-layout>
