<x-app-layout>

<div class="row">
    <div class="col-md-8 mx-auto">

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Sales Detail</h4>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-secondary">Back</a>
            </div>

            <div class="card-body">

                <h5 class="mb-3">Invoice Information</h5>

                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Invoice Number</th>
                        <td>{{ $sale->invoice_number }}</td>
                    </tr>
                    <tr>
                        <th>Customer Name</th>
                        <td>{{ $sale->customer_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Payment Method</th>
                        <td>{{ strtoupper($sale->payment_method) }}</td>
                    </tr>
                    <tr>
                        <th>Total Amount</th>
                        <td>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Paid Amount</th>
                        <td>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Change</th>
                        <td>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>

                <hr>

                <h5 class="mb-3">Items</h5>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="text-end mt-3">
                    <h4>Total: <strong>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</strong></h4>
                </div>

            </div>
        </div>

    </div>
</div>

</x-app-layout>
