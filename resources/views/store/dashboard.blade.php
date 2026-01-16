@extends('store.master_layout')

@section('store-content')
@php($currency = config('kutoot.currency_symbol', '₹'))
<section class="section">
    <div class="section-header">
        <h1>Store Dashboard</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('store.dashboard') }}" class="form-row">
                <div class="form-group col-md-3">
                    <label>From</label>
                    <input type="text" class="form-control datepicker-india" name="from" autocomplete="off" placeholder="DD-MM-YYYY" value="{{ $range['from'] }}">
                </div>
                <div class="form-group col-md-3">
                    <label>To</label>
                    <input type="text" class="form-control datepicker-india" name="to" autocomplete="off" placeholder="DD-MM-YYYY" value="{{ $range['to'] }}">
                </div>
                <div class="form-group col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary btn-block" type="submit">Apply</button>
                </div>
                <div class="form-group col-md-3 d-flex align-items-end">
                    <a class="btn btn-outline-secondary btn-block" href="{{ route('store.dashboard') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary"><i class="fas fa-coins"></i></div>
                <div class="card-wrap">
                    <div class="card-header"><h4>Total Sales</h4></div>
                    <div class="card-body">{{ $currency }} {{ number_format($kpis['totalSales'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success"><i class="fas fa-wallet"></i></div>
                <div class="card-wrap">
                    <div class="card-header"><h4>Store Balance</h4></div>
                    <div class="card-body">{{ $currency }} {{ number_format($kpis['sellerBalance'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="card-wrap">
                    <div class="card-header"><h4>Kutoot Balance (Commission)</h4></div>
                    <div class="card-body">{{ $currency }} {{ number_format($kpis['kutootBalance'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info"><i class="fas fa-percent"></i></div>
                <div class="card-wrap">
                    <div class="card-header"><h4>Discount Given</h4></div>
                    <div class="card-body">{{ $currency }} {{ number_format($kpis['totalDiscountGiven'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header"><h4>Conversion</h4></div>
                <div class="card-body">
                    {{ $kpis['conversionPercent'] }}%
                    <div class="text-muted mt-2">Range: {{ $range['from'] }} → {{ $range['to'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header"><h4>Coins Redeemed</h4></div>
                <div class="card-body">
                    <strong>{{ number_format($kpis['totalCoinsRedeemed']) }}</strong>
                    <div class="text-muted mt-1">Only when redeemed and amount ≥ min bill.</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header"><h4>Transactions</h4></div>
                <div class="card-body">
                    <div>Success: <strong>{{ $kpis['successCount'] }}</strong></div>
                    <div>Failed: <strong>{{ $kpis['failedCount'] }}</strong></div>
                    <div>Total Visitors: <strong>{{ $kpis['totalVisitors'] }}</strong></div>
                    <div>Redeemed Visitors: <strong>{{ $kpis['redeemedVisitors'] }}</strong></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header"><h4>Master Admin Settings</h4></div>
                <div class="card-body">
                    <div>Commission: <strong>{{ number_format($master['commissionPercent'], 2) }}%</strong></div>
                    <div>Discount: <strong>{{ number_format($master['discountPercent'], 2) }}%</strong></div>
                    <div>Min Bill: <strong>{{ $currency }} {{ number_format($master['minimumBillAmount'], 2) }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header"><h4>Revenue Trend</h4></div>
                <div class="card-body">
                    <canvas id="revenueChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="card-header"><h4>Success vs Failed</h4></div>
                <div class="card-body">
                    <canvas id="txnPie" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><h4>Visitors Trend</h4></div>
                <div class="card-body">
                    <canvas id="visitorsChart" height="90"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h4>How these KPIs are calculated</h4></div>
        <div class="card-body">
            <ul class="mb-0">
                <li><strong>Total Sales</strong>: {{ $formulas['totalSales'] }}</li>
                <li><strong>Kutoot Balance</strong>: {{ $formulas['kutootBalance'] }}</li>
                <li><strong>Store Balance</strong>: {{ $formulas['storeBalance'] }}</li>
                <li><strong>Discount Given</strong>: {{ $formulas['discountGiven'] }}</li>
                <li><strong>Coins Redeemed</strong>: {{ $formulas['coinsRedeemed'] }}</li>
                <li><strong>Coin to Money</strong>: {{ $formulas['coinToMoney'] }}</li>
                <li><strong>Conversion</strong>: {{ $formulas['conversion'] }}</li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const labels = @json($charts['labels']);
        const revenue = @json($charts['revenue']);
        const visitors = @json($charts['visitors']);
        const successCount = {{ (int) $charts['success'] }};
        const failedCount = {{ (int) $charts['failed'] }};

        const revenueCtx = document.getElementById('revenueChart');
        // Create gradient for revenue chart
        const revenueGradient = revenueCtx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        revenueGradient.addColorStop(0, 'rgba(234, 107, 30, 0.2)');
        revenueGradient.addColorStop(1, 'rgba(234, 107, 30, 0)');
        
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: revenue,
                    borderColor: '#EA6B1E',
                    backgroundColor: revenueGradient,
                    pointBackgroundColor: '#EA6B1E',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#EA6B1E',
                    pointHoverBorderColor: '#ffffff',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        display: true,
                        labels: {
                            color: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#FFF6EE' : '#3B322B'
                        }
                    } 
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        grid: {
                            color: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#4D3A2C' : '#E8DFD5'
                        },
                        ticks: {
                            color: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#FFF6EE' : '#3B322B'
                        }
                    },
                    x: {
                        grid: {
                            color: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#4D3A2C' : '#E8DFD5'
                        },
                        ticks: {
                            color: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#FFF6EE' : '#3B322B'
                        }
                    }
                }
            }
        });

        const visitorsCtx = document.getElementById('visitorsChart');
        new Chart(visitorsCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Visitors',
                    data: visitors,
                    backgroundColor: '#31D7A9',
                    borderColor: '#31D7A9',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                    hoverBackgroundColor: '#28C299',
                    hoverBorderColor: '#28C299'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        display: true,
                        labels: {
                            color: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#FFF6EE' : '#3B322B'
                        }
                    } 
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        grid: {
                            color: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#4D3A2C' : '#E8DFD5'
                        },
                        ticks: {
                            color: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#FFF6EE' : '#3B322B'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#FFF6EE' : '#3B322B'
                        }
                    }
                }
            }
        });

        const pieCtx = document.getElementById('txnPie');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['SUCCESS', 'FAILED'],
                datasets: [{
                    data: [successCount, failedCount],
                    backgroundColor: ['#31D7A9', '#C1272D'],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverBackgroundColor: ['#28C299', '#A81F24'],
                    hoverBorderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: {
                            color: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#FFF6EE' : '#3B322B',
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#2D1C10' : '#ffffff',
                        titleColor: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#FFF6EE' : '#3B322B',
                        bodyColor: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#FFF6EE' : '#3B322B',
                        borderColor: (document.documentElement.classList.contains('dark') || document.documentElement.hasAttribute('data-theme')) ? '#4D3A2C' : '#E8DFD5',
                        borderWidth: 1
                    }
                }
            }
        });
    </script>

    @push('scripts')
        <script>
            (function ($) {
                $(function () {
                    $('.datepicker-india').datepicker({
                        format: 'dd-mm-yyyy',
                        autoclose: true,
                        todayHighlight: true,
                        container: 'body',
                        zIndexOffset: 100000,
                        orientation: 'bottom auto'
                    });
                });
            })(jQuery);
        </script>
    @endpush
</section>
@endsection


