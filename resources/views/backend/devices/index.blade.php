{{-- resources/views/backend/devices/index.blade.php --}}
<x-app-layout>
<div class="row">
   <div class="col-sm-12">
      <div class="card">
         <div class="card-header d-flex justify-content-between">
            <div class="header-title">
               <h4 class="card-title">IOT Devices</h4>
            </div>
            <a href="{{ route('devices.create') }}" class="btn btn-primary">Add Device</a>
         </div>

         <div class="card-body">
            <div class="table-responsive">
               <table id="datatable" class="table table-striped">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Device ID</th>
                        <th>Status</th>
                        <th>QR Code</th>
                        <th>API URL</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($devices as $device)
                     <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                           <span class="badge bg-primary">{{ $device->device_id }}</span>
                        </td>
                        <td>
                           @if($device->status == 'active')
                              <span class="badge bg-success">Active</span>
                           @else
                              <span class="badge bg-danger">Inactive</span>
                           @endif
                        </td>
                        <td>
                           @php
                              $apiUrl = url("/api/cart/{$device->device_id}");
                              // Gunakan external QR code service
                              $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=" . urlencode($apiUrl);
                           @endphp
                           <img src="{{ $qrCodeUrl }}" 
                                alt="QR Code" 
                                style="width: 60px; height: 60px; cursor: pointer;"
                                onclick="showQRModal('{{ $device->device_id }}')">
                        </td>
                        <td>
                           <small class="text-muted">{{ $apiUrl }}</small>
                        </td>
                        <td>
                           <div class="btn-group" role="group">
                              <a href="{{ route('devices.edit', $device->id) }}" 
                                 class="btn btn-sm btn-warning">Edit</a>
                              <form action="{{ route('devices.destroy', $device->id) }}" 
                                    method="POST" 
                                    onsubmit="return confirm('Delete this device?')">
                                 @csrf
                                 @method('DELETE')
                                 <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                              </form>
                           </div>
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
   <div class="modal-dialog modal-sm">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="qrModalLabel">QR Code</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
         </div>
         <div class="modal-body text-center">
            <img id="qrImage" src="" style="width: 200px; height: 200px;">
            <p id="qrDeviceId" class="mt-2 fw-bold"></p>
            <p id="qrUrl" class="text-muted small"></p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="printQR()">Print</button>
         </div>
      </div>
   </div>
</div>

@push('scripts')
<script>
$(function() {
    $('#datatable').DataTable();
});

function showQRModal(deviceId) {
    const url = `{{ url('/api/cart') }}/${deviceId}`;
    const qrCode = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(url)}`;
    
    document.getElementById('qrImage').src = qrCode;
    document.getElementById('qrDeviceId').textContent = deviceId;
    document.getElementById('qrUrl').textContent = url;
    
    const modal = new bootstrap.Modal(document.getElementById('qrModal'));
    modal.show();
}

function printQR() {
    const qrImage = document.getElementById('qrImage').src;
    const deviceId = document.getElementById('qrDeviceId').textContent;
    const url = document.getElementById('qrUrl').textContent;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>QR Code ${deviceId}</title>
            <style>
                body { text-align: center; padding: 40px; font-family: Arial; }
                h2 { margin-bottom: 10px; }
                .url { margin-top: 20px; color: #666; font-size: 12px; word-break: break-all; }
                .instruction { margin-top: 20px; font-size: 12px; }
            </style>
        </head>
        <body>
            <h2>${deviceId}</h2>
            <img src="${qrImage}" style="width: 250px; height: 250px;">
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