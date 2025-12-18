<x-app-layout>

<div class="row">
    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Daftar Cart IoT</h4>
            </div>

            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Device ID</th>
                            <th>Total Item</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($carts as $cart)
                        @php 
                            $total = 0;
                            foreach($cart->items as $item){
                                $total += $item->product->price * $item->quantity;
                            }
                        @endphp
                        <tr>
                            <td>{{ $cart->device_id }}</td>
                            <td>{{ $cart->items->count() }} item</td>
                            <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('cart.detail', $cart->device_id) }}" 
                                   class="btn btn-primary btn-sm">
                                    Lihat Cart
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

    </div>
</div>

</x-app-layout>
