<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - {{ config('app.name', 'IoT Penjualan') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .success-container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .success-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 30px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7); }
            70% { box-shadow: 0 0 0 20px rgba(76, 175, 80, 0); }
            100% { box-shadow: 0 0 0 0 rgba(76, 175, 80, 0); }
        }
        
        .success-icon i {
            font-size: 60px;
            color: white;
        }
        
        h1 {
            color: #2E7D32;
            margin-bottom: 15px;
            font-size: 32px;
        }
        
        .subtitle {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        
        .invoice-details {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
            border-left: 5px solid #4CAF50;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .detail-label {
            color: #666;
            font-weight: 500;
        }
        
        .detail-value {
            color: #333;
            font-weight: 600;
        }
        
        .status-badge {
            display: inline-block;
            background-color: #e8f5e9;
            color: #2E7D32;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            flex: 1;
            padding: 16px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            border: none;
        }
        
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #3d8b40;
            transform: translateY(-3px);
            box-shadow: 0 7px 14px rgba(76, 175, 80, 0.2);
        }
        
        .btn-secondary {
            background-color: #f1f1f1;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .btn-secondary:hover {
            background-color: #e0e0e0;
            transform: translateY(-3px);
        }
        
        .footer-note {
            margin-top: 25px;
            color: #888;
            font-size: 14px;
        }
        
        .receipt-icon {
            animation: printAnim 2s infinite;
        }
        
        @keyframes printAnim {
            0% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0); }
        }
        
        @media (max-width: 600px) {
            .success-container {
                padding: 30px 20px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            h1 {
                font-size: 26px;
            }
        }
        
        .loading {
            display: none;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1>Pembayaran Berhasil!</h1>
        <p class="subtitle">Transaksi Anda telah selesai diproses dengan sukses</p>
        
        <div class="status-badge">
            <i class="fas fa-badge-check"></i> STATUS: <span id="status-text">COMPLETED</span>
        </div>
        
        <div class="invoice-details">
            <div class="detail-row">
                <span class="detail-label">No. Invoice</span>
                <span class="detail-value" id="invoice-number">{{ $invoice ?? 'Loading...' }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Tanggal Transaksi</span>
                <span class="detail-value" id="transaction-date">{{ $created_at ?? 'Loading...' }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Metode Pembayaran</span>
                <span class="detail-value" id="payment-method">{{ $payment_method ?? 'Loading...' }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Total Pembayaran</span>
                <span class="detail-value" id="total-amount">{{ $total ? 'Rp ' . number_format($total, 0, ',', '.') : 'Loading...' }}</span>
            </div>
            
            @if($paid_at ?? false)
            <div class="detail-row">
                <span class="detail-label">Waktu Pembayaran</span>
                <span class="detail-value" id="paid-at">{{ $paid_at }}</span>
            </div>
            @endif
            
            @if($paid_amount ?? false)
            <div class="detail-row">
                <span class="detail-label">Dibayar</span>
                <span class="detail-value" id="paid-amount">Rp {{ number_format($paid_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            
            @if($change_amount ?? false)
            <div class="detail-row">
                <span class="detail-label">Kembalian</span>
                <span class="detail-value" id="change-amount">Rp {{ number_format($change_amount, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
        
        <div class="action-buttons">
            <button class="btn btn-primary" onclick="printReceipt()" id="print-btn">
                <i class="fas fa-print receipt-icon"></i> Cetak Struk
            </button>
            
            <a href="{{ url('/') }}" class="btn btn-secondary">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
        </div>
        
        <p class="footer-note">
            <i class="fas fa-info-circle"></i> Struk digital telah dikirim ke email Anda
        </p>
    </div>

    <script>
        // Fungsi untuk mengambil parameter dari URL
        function getUrlParams() {
            const params = new URLSearchParams(window.location.search);
            return {
                invoice: params.get('invoice'),
                amount: params.get('amount'),
                method: params.get('method'),
                date: params.get('date')
            };
        }
        
        // Fungsi untuk memformat angka menjadi format Rupiah
        function formatRupiah(angka) {
            if (!angka) return 'Rp 0';
            return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
        }
        
        // Fungsi untuk mencetak struk
        function printReceipt() {
            const invoiceNumber = document.getElementById('invoice-number').textContent;
            const transactionDate = document.getElementById('transaction-date').textContent;
            const paymentMethod = document.getElementById('payment-method').textContent;
            const totalAmount = document.getElementById('total-amount').textContent;
            const statusText = document.getElementById('status-text').textContent;
            
            const paidAt = document.getElementById('paid-at') ? document.getElementById('paid-at').textContent : '';
            const paidAmount = document.getElementById('paid-amount') ? document.getElementById('paid-amount').textContent : '';
            const changeAmount = document.getElementById('change-amount') ? document.getElementById('change-amount').textContent : '';
            
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Struk Pembayaran - ${invoiceNumber}</title>
                    <style>
                        body { font-family: monospace; margin: 0; padding: 20px; }
                        .receipt { max-width: 300px; margin: 0 auto; }
                        .header { text-align: center; margin-bottom: 10px; }
                        .line { border-bottom: 1px dashed #000; margin: 10px 0; }
                        .detail { display: flex; justify-content: space-between; margin: 5px 0; }
                        .total { font-weight: bold; font-size: 1.2em; }
                        .footer { text-align: center; margin-top: 20px; font-size: 12px; }
                        @media print {
                            body { -webkit-print-color-adjust: exact; }
                        }
                    </style>
                </head>
                <body>
                    <div class="receipt">
                        <div class="header">
                            <h2>STRUK PEMBAYARAN</h2>
                            <p>{{ config('app.name', 'IoT Penjualan') }}</p>
                        </div>
                        <div class="line"></div>
                        <div class="detail"><span>No. Invoice:</span><span>${invoiceNumber}</span></div>
                        <div class="detail"><span>Tanggal:</span><span>${transactionDate}</span></div>
                        <div class="detail"><span>Metode:</span><span>${paymentMethod}</span></div>
                        <div class="line"></div>
                        ${paidAt ? `<div class="detail"><span>Waktu Bayar:</span><span>${paidAt}</span></div>` : ''}
                        <div class="detail"><span>Total:</span><span class="total">${totalAmount}</span></div>
                        ${paidAmount ? `<div class="detail"><span>Dibayar:</span><span>${paidAmount}</span></div>` : ''}
                        ${changeAmount ? `<div class="detail"><span>Kembalian:</span><span>${changeAmount}</span></div>` : ''}
                        <div class="line"></div>
                        <div class="detail"><span>Status:</span><span style="color: green; font-weight: bold;">${statusText}</span></div>
                        <div class="footer">
                            <p>Terima kasih atas pembelian Anda!</p>
                            <p>Struk ini sah sebagai bukti pembayaran</p>
                            <p>${new Date().toLocaleDateString('id-ID')} ${new Date().toLocaleTimeString('id-ID')}</p>
                        </div>
                    </div>
                </body>
                </html>
            `;
            
            const printWindow = window.open('', '_blank', 'width=350,height=600');
            printWindow.document.write(printContent);
            printWindow.document.close();
            
            // Tunggu sebentar agar konten terload
            setTimeout(() => {
                printWindow.focus();
                printWindow.print();
                
                // Optional: Tutup window setelah print
                setTimeout(() => {
                    printWindow.close();
                }, 1000);
            }, 500);
        }
        
        // Fungsi untuk efek confetti
        function showConfetti() {
            const confettiCount = 50;
            const container = document.body;
            
            for (let i = 0; i < confettiCount; i++) {
                const confetti = document.createElement('div');
                confetti.style.position = 'fixed';
                confetti.style.width = '10px';
                confetti.style.height = '10px';
                confetti.style.backgroundColor = getRandomColor();
                confetti.style.borderRadius = '50%';
                confetti.style.top = '-20px';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.zIndex = '9999';
                confetti.style.opacity = '0.8';
                
                container.appendChild(confetti);
                
                // Animasi confetti
                const animation = confetti.animate([
                    { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
                    { transform: `translateY(${window.innerHeight + 20}px) rotate(${360 + Math.random() * 360}deg)`, opacity: 0 }
                ], {
                    duration: 2000 + Math.random() * 2000,
                    easing: 'cubic-bezier(0.215, 0.61, 0.355, 1)'
                });
                
                animation.onfinish = () => confetti.remove();
            }
        }
        
        function getRandomColor() {
            const colors = ['#4CAF50', '#2E7D32', '#8BC34A', '#CDDC39', '#FFEB3B'];
            return colors[Math.floor(Math.random() * colors.length)];
        }
        
        // Inisialisasi halaman saat dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Tampilkan confetti setelah halaman dimuat
            setTimeout(showConfetti, 500);
            
            // Jika ada parameter di URL, update data
            const urlParams = getUrlParams();
            if (urlParams.invoice) {
                document.getElementById('invoice-number').textContent = urlParams.invoice;
            }
            if (urlParams.amount) {
                document.getElementById('total-amount').textContent = formatRupiah(urlParams.amount);
            }
            if (urlParams.method) {
                document.getElementById('payment-method').textContent = urlParams.method;
            }
            if (urlParams.date) {
                document.getElementById('transaction-date').textContent = urlParams.date;
            }
        });
    </script>
</body>
</html>