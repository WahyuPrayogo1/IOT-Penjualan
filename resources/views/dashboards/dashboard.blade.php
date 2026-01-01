<x-app-layout :assets="$assets ?? []">
   <div class="row">
      <div class="col-md-12 col-lg-12">
         <div class="row row-cols-1">
            <div class="d-slider1 overflow-hidden ">
                            <ul  class="swiper-wrapper list-inline m-0 p-0 mb-2">
                  <!-- Card 1: Total Sales -->
                  <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="700">
                     <div class="card-body">
                        <div class="progress-widget">
                           <div id="circle-progress-01" class="circle-progress-01 circle-progress circle-progress-primary text-center" data-min-value="0" data-max-value="100" data-value="{{ min(90, $salesGrowth + 50) }}" data-type="percent">
                              <svg class="card-slie-arrow " width="24" height="24px" viewBox="0 0 24 24">
                                 <path fill="currentColor" d="M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z" />
                              </svg>
                           </div>
                           <div class="progress-detail">
                              <p class="mb-2">Total Sales</p>
                              <h4 class="counter">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}</h4>
                        
                           </div>
                        </div>
                     </div>
                  </li>

                  <!-- Card 2: Today's Sales -->
                  <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="800">
                     <div class="card-body">
                        <div class="progress-widget">
                           <div id="circle-progress-02" class="circle-progress-01 circle-progress circle-progress-success text-center" data-min-value="0" data-max-value="100" data-value="{{ $transactionsToday > 0 ? min(80, $transactionsToday * 5) : 10 }}" data-type="percent">
                              <svg class="card-slie-arrow " width="24" height="24" viewBox="0 0 24 24">
                                 <path fill="currentColor" d="M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z" />
                              </svg>
                           </div>
                           <div class="progress-detail">
                              <p class="mb-2">Today's Sales</p>
                              <h4 class="counter">Rp {{ number_format($salesToday ?? 0, 0, ',', '.') }}</h4>
                            
                           </div>
                        </div>
                     </div>
                  </li>

                  <!-- Card 3: Monthly Sales -->
                  <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="900">
                     <div class="card-body">
                        <div class="progress-widget">
                           <div id="circle-progress-03" class="circle-progress-01 circle-progress circle-progress-info text-center" data-min-value="0" data-max-value="100" data-value="{{ min(70, ($salesThisMonth / max($totalSales, 1)) * 100) }}" data-type="percent">
                              <svg class="card-slie-arrow " width="24" viewBox="0 0 24 24">
                                 <path fill="currentColor" d="M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z" />
                              </svg>
                           </div>
                           <div class="progress-detail">
                              <p class="mb-2">This Month</p>
                              <h4 class="counter">Rp {{ number_format($salesThisMonth ?? 0, 0, ',', '.') }}</h4>
                            
                           </div>
                        </div>
                     </div>
                  </li>

                  <!-- Card 4: Products -->
                  <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="1000">
                     <div class="card-body">
                        <div class="progress-widget">
                           <div id="circle-progress-04" class="circle-progress-01 circle-progress circle-progress-warning text-center" data-min-value="0" data-max-value="100" data-value="{{ $totalProducts > 0 ? ($activeProducts / $totalProducts * 100) : 0 }}" data-type="percent">
                              <svg class="card-slie-arrow " width="24px" height="24px" viewBox="0 0 24 24">
                                 <path fill="currentColor" d="M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z" />
                              </svg>
                           </div>
                           <div class="progress-detail">
                              <p class="mb-2">Products</p>
                              <h4 class="counter">{{ $totalProducts ?? 0 }}</h4>
                             
                           </div>
                        </div>
                     </div>
                  </li>

                  <!-- Card 5: Orders Served -->
                  <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="1100">
                     <div class="card-body">
                        <div class="progress-widget">
                           <div id="circle-progress-05" class="circle-progress-01 circle-progress circle-progress-danger text-center" data-min-value="0" data-max-value="100" data-value="{{ min(50, ($orderServed / max($orderServed + 100, 1)) * 100) }}" data-type="percent">
                              <svg class="card-slie-arrow " width="24px" height="24px" viewBox="0 0 24 24">
                                 <path fill="currentColor" d="M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z" />
                              </svg>
                           </div>
                           <div class="progress-detail">
                              <p class="mb-2">Orders Served</p>
                              <h4 class="counter">{{ number_format($orderServed ?? 0) }}</h4>
                           </div>
                        </div>
                     </div>
                  </li>

                  
               </ul>
               <div class="swiper-button swiper-button-next"></div>
               <div class="swiper-button swiper-button-prev"></div>
            </div>
         </div>
      </div>

      <!-- Main Content Area -->
      <div class="col-md-12 col-lg-8">
         <div class="row">
            <!-- Weekly Sales Chart -->
            <div class="col-md-12">
               <div class="card" data-aos="fade-up" data-aos-delay="800">
                  <div class="card-header d-flex justify-content-between flex-wrap">
                     <div class="header-title">
                        <h4 class="card-title">Weekly Sales</h4>
                        <p class="mb-0">{{ now()->startOfWeek()->format('M d') }} - {{ now()->endOfWeek()->format('M d, Y') }}</p>
                     </div>
                     <div class="dropdown">
                        <a href="#" class="text-secondary dropdown-toggle" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                           This Week
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton2">
                           <li><a class="dropdown-item" href="#" onclick="loadChartData('today')">Today</a></li>
                           <li><a class="dropdown-item" href="#" onclick="loadChartData('week')">This Week</a></li>
                           <li><a class="dropdown-item" href="#" onclick="loadChartData('month')">This Month</a></li>
                        </ul>
                     </div>
                  </div>
                  <div class="card-body">
                     <!-- Container untuk chart saja, tanpa chart manual -->
                     <div id="weekly-sales-chart" style="min-height: 300px;"></div>
                  </div>
               </div>
            </div>

            <!-- Top Products -->
            <div class="col-md-12 col-lg-6">
               <div class="card" data-aos="fade-up" data-aos-delay="1000">
                  <div class="card-header d-flex justify-content-between flex-wrap">
                     <div class="header-title">
                        <h4 class="card-title">Top Products</h4>
                        <p class="mb-0">{{ now()->format('F Y') }}</p>
                     </div>
                     <div class="dropdown">
                        <a href="#" class="text-secondary dropdown-toggle" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                           This Month
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                           <li><a class="dropdown-item" href="#" onclick="loadTopProducts('today')">Today</a></li>
                           <li><a class="dropdown-item" href="#" onclick="loadTopProducts('week')">This Week</a></li>
                           <li><a class="dropdown-item" href="#" onclick="loadTopProducts('month')">This Month</a></li>
                        </ul>
                     </div>
                  </div>
                  <div class="card-body">
                     <div id="top-products-list">
                        @if($topProducts->count() > 0)
                           @foreach($topProducts as $item)
                           <div class="d-flex align-items-center justify-content-between mb-3">
                              <div class="d-flex align-items-center" style="width: 60%;">
                                 <div class="avatar bg-soft-primary rounded me-3">
                                    <span class="text-primary fs-6">{{ substr($item->product->name ?? 'N/A', 0, 1) }}</span>
                                 </div>
                                 <div style="overflow: hidden;">
                                    <h6 class="mb-0 text-truncate" style="max-width: 150px;">{{ $item->product->name ?? 'Unknown' }}</h6>
                                    <small class="text-secondary">{{ $item->product->barcode ?? 'N/A' }}</small>
                                 </div>
                              </div>
                              <div class="text-end">
                                 <h6 class="mb-0">{{ $item->total_quantity }} sold</h6>
                                 <small class="text-secondary">
                                    Rp {{ number_format($item->product->price ?? 0, 0, ',', '.') }}
                                 </small>
                              </div>
                           </div>
                           @endforeach
                        @else
                           <div class="text-center py-4">
                              <p class="text-muted">No sales data available</p>
                           </div>
                        @endif
                     </div>
                  </div>
               </div>
            </div>

            <!-- Today's Activity -->
            <div class="col-md-12 col-lg-6">
               <div class="card" data-aos="fade-up" data-aos-delay="1200">
                  <div class="card-header d-flex justify-content-between flex-wrap">
                     <div class="header-title">
                        <h4 class="card-title">Today's Activity</h4>
                        <p class="mb-0">{{ now()->format('l, d F Y') }}</p>
                     </div>
                     <div class="dropdown">
                        <a href="#" class="text-secondary dropdown-toggle" id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-expanded="false">
                           {{ now()->format('d M Y') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton3">
                           <li><a class="dropdown-item" href="#" onclick="loadTodayActivity('today')">Today</a></li>
                           <li><a class="dropdown-item" href="#" onclick="loadTodayActivity('yesterday')">Yesterday</a></li>
                           <li><a class="dropdown-item" href="#" onclick="loadTodayActivity('week')">This Week</a></li>
                        </ul>
                     </div>
                  </div>
                  <div class="card-body">
                     <div class="d-flex justify-content-between mb-4">
                        <div class="text-center">
                           <div class="p-3 rounded bg-soft-primary mb-2">
                              <i class="fa fa-shopping-cart text-primary fs-4"></i>
                           </div>
                           <h3 class="text-primary">{{ $todayActivities['transactions'] ?? 0 }}</h3>
                           <small class="text-secondary">Transactions</small>
                        </div>
                        <div class="text-center">
                           <div class="p-3 rounded bg-soft-success mb-2">
                              <i class="fa fa-money-bill-wave text-success fs-4"></i>
                           </div>
                           <h3 class="text-success">{{ $todayActivities['sales'] ? 'Rp ' . number_format($todayActivities['sales']/1000000, 1) . 'M' : '0' }}</h3>
                           <small class="text-secondary">Sales</small>
                        </div>
                        <div class="text-center">
                           <div class="p-3 rounded bg-soft-info mb-2">
                              <i class="fa fa-box text-info fs-4"></i>
                           </div>
                           <h3 class="text-info">{{ $todayActivities['orders'] ?? 0 }}</h3>
                           <small class="text-secondary">Items Sold</small>
                        </div>
                     </div>
                     
                     <!-- Progress bars untuk visualisasi -->
                     <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                           <small>Sales Target</small>
                           <small>75%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                           <div class="progress-bar bg-primary" style="width: 75%"></div>
                        </div>
                     </div>
                     <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                           <small>Transaction Target</small>
                           <small>60%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                           <div class="progress-bar bg-success" style="width: 60%"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Recent Transactions -->
            <div class="col-md-12 col-lg-12">
               <div class="card overflow-hidden" data-aos="fade-up" data-aos-delay="400">
                  <div class="card-header d-flex justify-content-between flex-wrap">
                     <div class="header-title">
                        <h4 class="card-title mb-2">Recent Transactions</h4>
                        <p class="mb-0">
                           <i class="fa fa-history text-primary me-2"></i>
                           {{ $totalTransactions ?? 0 }} total transactions
                        </p>
                     </div>
                     <div class="dropdown">
                        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-primary">
                           <i class="fa fa-eye me-1"></i> View All
                        </a>
                     </div>
                  </div>
                  <div class="card-body p-0">
                     <div class="table-responsive">
                        <table class="table table-hover mb-0">
                           <thead class="bg-light">
                              <tr>
                                 <th>INVOICE</th>
                                 <th>CUSTOMER</th>
                                 <th>AMOUNT</th>
                                 <th>PAYMENT</th>
                                 <th>TIME</th>
                              </tr>
                           </thead>
                           <tbody>
                              @forelse($recentSales as $sale)
                              <tr>
                                 <td>
                                    <span class="badge bg-primary">#{{ $sale->invoice_number }}</span>
                                 </td>
                                 <td>
                                    <div>
                                       <strong>{{ $sale->customer_name ?: 'Walk-in' }}</strong>
                                       <br>
                                       <small class="text-muted">{{ $sale->items->count() }} items</small>
                                    </div>
                                 </td>
                                 <td>
                                    <strong class="text-success">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</strong>
                                 </td>
                                 <td>
                                    <span class="badge bg-{{ $sale->payment_method == 'cash' ? 'success' : 'info' }}">
                                       {{ strtoupper($sale->payment_method) }}
                                    </span>
                                 </td>
                                 <td>
                                    <small>{{ $sale->created_at->format('H:i') }}</small><br>
                                    <small class="text-muted">{{ $sale->created_at->format('d/m') }}</small>
                                 </td>
                              </tr>
                              @empty
                              <tr>
                                 <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                       <i class="fa fa-receipt fa-2x mb-2"></i>
                                       <p>No transactions yet</p>
                                    </div>
                                 </td>
                              </tr>
                              @endforelse
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Sidebar -->
      <div class="col-md-12 col-lg-4">
         <div class="row">
            <!-- System Summary -->
            <div class="col-md-12">
               <div class="card" data-aos="fade-up" data-aos-delay="900">
                  <div class="card-header">
                     <h4 class="card-title mb-0">POS System Summary</h4>
                  </div>
                  <div class="card-body">
                     <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                           <div>
                              <h2 class="text-primary mb-0">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}</h2>
                              <p class="text-secondary mb-0">Lifetime Sales</p>
                           </div>
                           <div class="text-end">
                              <span class="badge bg-{{ $salesGrowth >= 0 ? 'success' : 'danger' }} rounded-pill fs-6">
                                 {{ $salesGrowth >= 0 ? '+' : '' }}{{ number_format($salesGrowth, 1) }}%
                              </span>
                              <p class="text-muted mt-1 mb-0">Growth</p>
                           </div>
                        </div>
                        
                        <div class="row text-center mt-4">
                           <div class="col-6 border-end">
                              <h4 class="text-success">{{ $transactionsToday ?? 0 }}</h4>
                              <small class="text-secondary">Today's Transactions</small>
                           </div>
                           <div class="col-6">
                              <h4 class="text-info">{{ $activeCarts ?? 0 }}</h4>
                              <small class="text-secondary">Active Carts</small>
                           </div>
                        </div>
                     </div>
                     
                     <div class="d-grid gap-2">
                        <a href="{{ route('sales.create') }}" class="btn btn-primary">
                           <i class="fa fa-plus-circle me-1"></i> New Sale
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                           <i class="fa fa-box me-1"></i> Manage Products
                        </a>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Quick Stats -->
            <div class="col-md-12 mt-3">
               <div class="card" data-aos="fade-up" data-aos-delay="300">
                  <div class="card-header">
                     <h5 class="card-title mb-0">Quick Stats</h5>
                  </div>
                  <div class="card-body">
                     <div class="row">
                        <div class="col-6 text-center border-end">
                           <div class="p-3">
                              <div class="avatar bg-soft-primary rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; line-height: 50px;">
                                 <i class="fa fa-boxes text-primary fs-4"></i>
                              </div>
                              <h4 class="mb-0">{{ $totalProducts ?? 0 }}</h4>
                              <small class="text-secondary">Products</small>
                           </div>
                        </div>
                        <div class="col-6 text-center">
                           <div class="p-3">
                              <div class="avatar bg-soft-success rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; line-height: 50px;">
                                 <i class="fa fa-check-circle text-success fs-4"></i>
                              </div>
                              <h4 class="mb-0">{{ number_format($orderServed ?? 0) }}</h4>
                              <small class="text-secondary">Orders Served</small>
                           </div>
                        </div>
                     </div>
                     
                     <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                           <small>Stock Availability</small>
                           <small>{{ $totalProducts > 0 ? round(($activeProducts/$totalProducts)*100) : 0 }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                           <div class="progress-bar bg-warning" 
                                style="width: {{ $totalProducts > 0 ? ($activeProducts/$totalProducts)*100 : 0 }}%"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Alerts -->
            <div class="col-md-12 mt-3">
               <div class="card border-warning" data-aos="fade-up" data-aos-delay="400">
                  <div class="card-header bg-warning bg-opacity-10 border-warning">
                     <h5 class="card-title mb-0 text-warning">
                        <i class="fa fa-exclamation-triangle me-2"></i>Alerts
                     </h5>
                  </div>
                  <div class="card-body">
                     @if($lowStockProducts > 0)
                     <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                        <div class="d-flex align-items-center">
                           <i class="fa fa-exclamation-circle me-3 fs-4"></i>
                           <div>
                              <h6 class="alert-heading mb-1">Low Stock Alert</h6>
                              <p class="mb-0">{{ $lowStockProducts }} products need restocking</p>
                           </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                     @endif
                     
                     @if($activeCarts > 0)
                     <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                           <i class="fa fa-shopping-cart me-3 fs-4"></i>
                           <div>
                              <h6 class="alert-heading mb-1">Active Carts</h6>
                              <p class="mb-0">{{ $activeCarts }} carts waiting for checkout</p>
                           </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                     @endif
                     
                     @if($lowStockProducts == 0 && $activeCarts == 0)
                     <div class="text-center py-3">
                        <i class="fa fa-check-circle text-success fs-1 mb-3"></i>
                        <p class="text-muted mb-0">All systems operational</p>
                        <small>No alerts at this time</small>
                     </div>
                     @endif
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   @push('scripts')
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         // Inisialisasi chart hanya sekali
         initWeeklySalesChart();
         
         // Auto-refresh data dashboard
         setInterval(updateDashboardData, 60000); // setiap 1 menit
         
         // Inisialisasi tooltips
         var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
         var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
         });
      });

      // Fungsi untuk chart sales mingguan
      function initWeeklySalesChart() {
         // Hapus chart yang sudah ada (jika ada) untuk menghindari double
         if (window.weeklySalesChart) {
            window.weeklySalesChart.destroy();
         }
         
         // Data dari controller
         var chartLabels = @json($chartLabels);
         var chartData = @json($chartData);
         
         // Format data untuk chart
         var formattedData = chartData.map(function(value) {
            return value / 1000; // Convert to thousands for better display
         });
         
         var options = {
            series: [{
               name: 'Sales (in thousands)',
               data: formattedData
            }],
            chart: {
               type: 'bar',
               height: 300,
               toolbar: {
                  show: false
               }
            },
            plotOptions: {
               bar: {
                  borderRadius: 4,
                  horizontal: false,
                  columnWidth: '60%',
               }
            },
            dataLabels: {
               enabled: false
            },
            stroke: {
               show: true,
               width: 2,
               colors: ['transparent']
            },
            xaxis: {
               categories: chartLabels,
            },
            yaxis: {
               title: {
                  text: 'Sales (Rp thousands)'
               },
               labels: {
                  formatter: function(value) {
                     return 'Rp ' + value.toLocaleString();
                  }
               }
            },
            fill: {
               opacity: 1
            },
            tooltip: {
               y: {
                  formatter: function(value) {
                     return 'Rp ' + (value * 1000).toLocaleString();
                  }
               }
            },
            colors: ['#3a57e8']
         };

         // Buat chart hanya jika elemen ada
         var chartElement = document.querySelector("#weekly-sales-chart");
         if (chartElement) {
            window.weeklySalesChart = new ApexCharts(chartElement, options);
            window.weeklySalesChart.render();
         }
      }

      // Fungsi untuk update data real-time
      function updateDashboardData() {
         fetch('{{ route("dashboard.data") }}')
            .then(response => response.json())
            .then(data => {
               // Update Today's Sales card
               let salesTodayElement = document.querySelector('.counter:contains("Rp")');
               if (salesTodayElement) {
                  animateCounter(salesTodayElement, data.sales_today);
               }
               
               // Update transactions count
               let transactionsElement = document.querySelector('.text-primary:contains("Transactions") + h3');
               if (transactionsElement) {
                  transactionsElement.textContent = data.transactions_today;
               }
               
               // Update active carts
               let activeCartsElement = document.querySelector('.counter:contains("Active Carts")');
               if (activeCartsElement) {
                  animateCounter(activeCartsElement, data.active_carts);
               }
               
               // Update orders today
               let ordersElement = document.querySelector('.text-info:contains("Items Sold") + h3');
               if (ordersElement) {
                  ordersElement.textContent = data.orders_today;
               }
            })
            .catch(error => console.error('Error updating dashboard:', error));
      }

      // Fungsi animasi counter
      function animateCounter(element, newValue) {
         if (!element || typeof newValue === 'undefined') return;
         
         const currentText = element.textContent;
         const currentValue = parseInt(currentText.replace(/[^0-9]/g, '')) || 0;
         
         if (currentValue === newValue) return;
         
         const duration = 1000;
         const start = currentValue;
         const increment = (newValue - start) / (duration / 16);
         let current = start;
         
         const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= newValue) || (increment < 0 && current <= newValue)) {
               current = newValue;
               clearInterval(timer);
            }
            
            // Format number jika nilai besar
            if (element.textContent.includes('Rp')) {
               element.textContent = 'Rp ' + Math.round(current).toLocaleString();
            } else {
               element.textContent = Math.round(current).toLocaleString();
            }
         }, 16);
      }

      // Fungsi untuk filter data (placeholder)
      function loadChartData(range) {
         alert('Loading chart data for: ' + range);
         // Implement AJAX call here
      }

      function loadTopProducts(range) {
         alert('Loading top products for: ' + range);
         // Implement AJAX call here
      }

      function loadTodayActivity(range) {
         alert('Loading activity for: ' + range);
         // Implement AJAX call here
      }
   </script>
   @endpush
</x-app-layout>