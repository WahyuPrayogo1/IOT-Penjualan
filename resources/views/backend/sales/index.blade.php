<x-app-layout>
<div class="row">
   <div class="col-sm-12">
      <div class="card">
         <div class="card-header d-flex justify-content-between">
            <h4 class="card-title">Sales</h4>
            <div>
                <!-- Filter Status -->
                <select id="statusFilter" class="form-select me-2" style="width: auto; display: inline-block;">
                    <option value="">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                
                <a href="{{ route('sales.create') }}" class="btn btn-primary">Add Sale</a>
            </div>
         </div>

         <div class="card-body">
            <div class="table-responsive">
               <table id="datatable" class="table table-striped">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Device</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
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
    // Initialize DataTable
    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('sales.index') }}",
            data: function (d) {
                d.status = $('#statusFilter').val(); // Filter by status
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
            { 
                data: 'invoice_number', 
                name: 'invoice_number',
                render: function(data, type, row) {
                    return '<strong>' + data + '</strong>';
                }
            },
            { 
                data: 'customer_name', 
                name: 'customer_name',
                render: function(data) {
                    return data || '<em>No Name</em>';
                }
            },
            { 
                data: 'device_id', 
                name: 'device_id',
                render: function(data) {
                    return data ? '<span class="badge bg-primary">' + data + '</span>' : '-';
                }
            },
            { 
                data: 'total_amount', 
                name: 'total_amount',
                render: function(data) {
                    return new Intl.NumberFormat('id-ID', { 
                        style: 'currency', 
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(data);
                }
            },
            { 
                data: 'payment_method', 
                name: 'payment_method',
                render: function(data) {
                    if (data === 'midtrans') {
                        return '<span class="badge bg-info">Midtrans</span>';
                    } else if (data === 'cash') {
                        return '<span class="badge bg-success">Cash</span>';
                    }
                    return data;
                }
            },
            { 
                data: 'status_badge', // PAKAI 'status_badge' bukan 'status'
                name: 'status',
                orderable: true,
                searchable: true
            },
            { 
                data: 'date', 
                name: 'created_at',
                render: function(data) {
                    return data;
                }
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable:false, 
                searchable:false 
            }
        ],
        order: [[7, 'desc']], // Sort by date descending
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
    
    // Filter by status
    $('#statusFilter').change(function() {
        table.ajax.reload();
    });
    
    // Handle cancel payment button
    $(document).on('click', '.cancel-payment', function() {
        var saleId = $(this).data('id');
        var invoice = $(this).data('invoice');
        
        if (confirm('Cancel payment for invoice ' + invoice + '?')) {
            $.ajax({
                url: '/api/payment/cancel',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    sale_id: saleId,
                    invoice: invoice
                },
                success: function(response) {
                    if (response.success) {
                        alert('Payment cancelled successfully');
                        table.ajax.reload();
                    } else {
                        alert('Failed to cancel payment: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error cancelling payment');
                }
            });
        }
    });
});
</script>

<style>
.badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}
</style>
@endpush
</x-app-layout>