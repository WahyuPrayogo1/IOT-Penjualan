<x-app-layout>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Generate QR Codes</h4>
                        <p class="mb-0">Print and paste these QR codes on shopping carts</p>
                    </div>
                    <div>
                        <a href="{{ route('devices.index') }}" class="btn btn-secondary">Back</a>
                        <a href="{{ route('devices.print-all') }}" class="btn btn-primary" target="_blank">
                            Print All
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        @foreach($devices as $device)
                        <div class="col-md-3 mb-4">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $device->device_id }}</h5>
                                    
                                    @php
                                        $url = url("/api/cart/{$device->device_id}");
                                        $qrCode = QrCode::size(150)->generate($url);
                                    @endphp
                                    
                                    <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" 
                                         alt="QR Code" 
                                         class="img-fluid mb-2">
                                    
                                    <p class="text-muted small mb-2">
                                        {{ Str::limit($url, 30) }}
                                    </p>
                                    
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" 
                                                onclick="printSingleQR('{{ $device->device_id }}', '{{ $url }}')">
                                            Print
                                        </button>
                                        <a href="data:image/png;base64,{{ base64_encode($qrCode) }}" 
                                           download="{{ $device->device_id }}.png"
                                           class="btn btn-outline-secondary">
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function printSingleQR(deviceId, url) {
        const qrCode = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(url)}`;
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>${deviceId} - QR Code</title>
                <style>
                    @page { margin: 0; }
                    body { text-align: center; padding: 30px; font-family: Arial; }
                    h2 { margin: 10px 0; }
                    .qr-container { margin: 20px auto; }
                    .url { margin-top: 15px; color: #666; font-size: 11px; word-break: break-all; }
                    .instruction { margin-top: 20px; font-size: 12px; }
                </style>
            </head>
            <body>
                <h2>${deviceId}</h2>
                <div class="qr-container">
                    <img src="${qrCode}" style="width: 250px; height: 250px;">
                </div>
                <p class="url">${url}</p>
                <div class="instruction">
                    <p><strong>Instructions:</strong></p>
                    <p>1. Scan this QR code with mobile app</p>
                    <p>2. Use IOT scanner on this cart</p>
                    <p>3. View cart and checkout via mobile</p>
                </div>
                <hr style="margin: 30px 0;">
                <small>Segar Mart - Self Checkout System</small>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
    </script>
    @endpush
</x-app-layout>