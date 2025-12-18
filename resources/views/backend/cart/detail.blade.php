<x-app-layout>

<div class="row">
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Cart Device: {{ $device_id }}</h4>

                <form action="{{ url('/api/cart/'.$device_id) }}" method="POST" 
                      onsubmit="return confirm('Clear cart?');">
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Clear Cart</button>
                </form>
            </div>

            <div class="card-body">

                @if(!$cart || $cart->items->isEmpty())
                    <div class="alert alert-info">
                        Cart kosong untuk device ini.
                    </div>
                @else

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Barcode</th>
                            <th>Nama</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php $total = 0; @endphp

                        @foreach($cart->items as $item)
                        @php 
                            $subtotal = $item->product->price * $item->quantity;
                            $total += $subtotal;
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product->barcode }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp {{ number_format($item->product->price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>

                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Total</th>
                            <th>Rp {{ number_format($total, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>

                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-primary">Checkout Cash</a>
                    <a href="#" class="btn btn-success">Checkout Midtrans</a>
                </div>

                @endif

            </div>
        </div>

    </div>
</div>

</x-app-layout>
