@extends('admin.master_layout')

@section('title')
<title>{{__('Coin Ledger Dashboard')}}</title>
@endsection

@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><i class="fas fa-coins"></i> {{__('Coin Ledger Dashboard')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
                <div class="breadcrumb-item">{{__('Coin Ledger')}}</div>
            </div>
        </div>

        <div class="section-body">
            <!-- Outstanding Liability Cards -->
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Outstanding Liability</h4>
                            </div>
                            <div class="card-body">
                                {{ number_format($summary['outstanding']['total_liability']) }} coins
                                <small class="text-muted d-block">
                                    {{ $currencySymbol }}{{ number_format($summary['outstanding']['total_liability'] * $coinValue, 2) }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Paid Coin Liability</h4>
                            </div>
                            <div class="card-body">
                                {{ number_format($summary['outstanding']['paid_liability']) }} coins
                                <small class="text-muted d-block">
                                    {{ $currencySymbol }}{{ number_format($summary['outstanding']['paid_liability'] * $coinValue, 2) }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Reward Coin Liability</h4>
                            </div>
                            <div class="card-body">
                                {{ number_format($summary['outstanding']['reward_liability']) }} coins
                                <small class="text-muted d-block">
                                    {{ $currencySymbol }}{{ number_format($summary['outstanding']['reward_liability'] * $coinValue, 2) }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today & This Month Stats -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-calendar-day"></i> Today's Activity</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h5 class="text-success">+{{ number_format($summary['today']['credits']) }}</h5>
                                        <small class="text-muted">Credits</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h5 class="text-danger">-{{ number_format($summary['today']['redemptions']) }}</h5>
                                        <small class="text-muted">Redemptions</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-calendar-alt"></i> This Month</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="text-center">
                                        <h5 class="text-success">+{{ number_format($summary['this_month']['credits']) }}</h5>
                                        <small class="text-muted">Credits</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <h5 class="text-danger">-{{ number_format($summary['this_month']['redemptions']) }}</h5>
                                        <small class="text-muted">Redemptions</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <h5 class="text-warning">-{{ number_format($summary['this_month']['expired']) }}</h5>
                                        <small class="text-muted">Expired</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- All Time Stats -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-chart-bar"></i> All Time Statistics</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 col-md-4 col-6 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h4 class="text-success">{{ number_format($summary['all_time']['paid_credits']) }}</h4>
                                        <small class="text-muted">Paid Credits</small>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-4 col-6 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h4 class="text-info">{{ number_format($summary['all_time']['reward_credits']) }}</h4>
                                        <small class="text-muted">Reward Credits</small>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-4 col-6 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h4 class="text-danger">{{ number_format($summary['all_time']['paid_redeemed']) }}</h4>
                                        <small class="text-muted">Paid Redeemed</small>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-4 col-6 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h4 class="text-warning">{{ number_format($summary['all_time']['reward_redeemed']) }}</h4>
                                        <small class="text-muted">Reward Redeemed</small>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-4 col-6 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h4 class="text-secondary">{{ number_format($summary['all_time']['paid_expired']) }}</h4>
                                        <small class="text-muted">Paid Expired</small>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-4 col-6 mb-3">
                                    <div class="text-center p-3 border rounded">
                                        <h4 class="text-secondary">{{ number_format($summary['all_time']['reward_expired']) }}</h4>
                                        <small class="text-muted">Reward Expired</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Stats -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-users"></i> User Statistics</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h3>{{ number_format($summary['users']['total_active']) }}</h3>
                                        <small class="text-muted">Total Users with Transactions</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h3>{{ number_format($summary['users']['with_balance']) }}</h3>
                                        <small class="text-muted">Users with Active Balance</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-cogs"></i> Quick Actions</h4>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('admin.coin-ledger.system-wallet') }}" class="btn btn-dark mr-2 mb-2">
                                <i class="fas fa-building"></i> Kutoot System Wallet
                            </a>
                            <a href="{{ route('admin.coin-ledger.index') }}" class="btn btn-primary mr-2 mb-2">
                                <i class="fas fa-list"></i> View All Transactions
                            </a>
                            <a href="{{ route('admin.coin-ledger.export') }}" class="btn btn-success mr-2 mb-2">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </a>
                            <a href="{{ route('admin.coin-ledger.zoho-mapping') }}" class="btn btn-info mb-2">
                                <i class="fas fa-book"></i> Zoho Mapping Guide
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Flow Chart -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-chart-line"></i> Daily Coin Flow (Last 30 Days)</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="dailyFlowChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zoho Mapping Reference -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-book"></i> Zoho Account Mapping Reference</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Coin Category</th>
                                            <th>Entry Type</th>
                                            <th>Zoho Account</th>
                                            <th>Meaning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge badge-success">PAID</span></td>
                                            <td>CREDIT</td>
                                            <td>Coin Liability</td>
                                            <td>User purchased coins - creates liability</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge badge-warning">REWARD</span></td>
                                            <td>CREDIT</td>
                                            <td>Marketing Liability</td>
                                            <td>Free/bonus coins - marketing expense liability</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge badge-success">PAID</span></td>
                                            <td>REDEEM</td>
                                            <td>Discount Expense</td>
                                            <td>Paid coins used - liability squared off as discount</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge badge-warning">REWARD</span></td>
                                            <td>REDEEM</td>
                                            <td>Marketing Expense</td>
                                            <td>Reward coins used - liability squared off</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge badge-secondary">ANY</span></td>
                                            <td>EXPIRE</td>
                                            <td>Liability Write-off</td>
                                            <td>Expired unused coins - liability written off (income)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Flow Chart
    const dailyFlowData = @json($dailyFlow);

    const ctx = document.getElementById('dailyFlowChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyFlowData.map(d => d.date),
            datasets: [
                {
                    label: 'Paid Credits',
                    data: dailyFlowData.map(d => d.paid_credits),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Reward Credits',
                    data: dailyFlowData.map(d => d.reward_credits),
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Paid Redeemed',
                    data: dailyFlowData.map(d => -d.paid_redeemed),
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Reward Redeemed',
                    data: dailyFlowData.map(d => -d.reward_redeemed),
                    borderColor: '#fd7e14',
                    backgroundColor: 'rgba(253, 126, 20, 0.1)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
</script>
@endsection
