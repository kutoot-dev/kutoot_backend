@extends('admin.master_layout')

@section('title')
<title>{{__('Kutoot System Wallet')}}</title>
@endsection

@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><i class="fas fa-building"></i> {{__('Kutoot System Wallet')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.coin-ledger.summary') }}">{{__('Coin Ledger')}}</a></div>
                <div class="breadcrumb-item">{{__('System Wallet')}}</div>
            </div>
        </div>

        <div class="section-body">
            <!-- System Wallet Balance Cards -->
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Kutoot Liability</h4>
                            </div>
                            <div class="card-body">
                                {{ number_format($systemBalance['total']) }} coins
                                <small class="text-muted d-block">
                                    {{ $currencySymbol }}{{ number_format($systemBalance['total'] * $coinValue, 2) }}
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
                                {{ number_format($systemBalance['paid']) }} coins
                                <small class="text-muted d-block">
                                    {{ $currencySymbol }}{{ number_format($systemBalance['paid'] * $coinValue, 2) }}
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
                                {{ number_format($systemBalance['reward']) }} coins
                                <small class="text-muted d-block">
                                    {{ $currencySymbol }}{{ number_format($systemBalance['reward'] * $coinValue, 2) }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liability Adjustment Forms -->
            <div class="row">
                <!-- Set Target Liability -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-crosshairs"></i> Set Target Liability</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.coin-ledger.update-liability') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-control" required>
                                        @foreach($categories as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Target Amount (Coins) <span class="text-danger">*</span></label>
                                    <input type="number" name="target_amount" class="form-control"
                                           placeholder="e.g., 50000000 for 5 crore" min="0" required>
                                    <small class="text-muted">Enter the total target liability in coins</small>
                                </div>
                                <div class="form-group">
                                    <label>Reason <span class="text-danger">*</span></label>
                                    <textarea name="reason" class="form-control" rows="2"
                                              placeholder="Reason for adjustment" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Liability
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Adjust Liability -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-plus-minus"></i> Adjust Liability</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.coin-ledger.adjust-liability') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-control" required>
                                        @foreach($categories as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Adjustment Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="adjustment" class="form-control"
                                           placeholder="Positive to add, negative to subtract" required>
                                    <small class="text-muted">Use positive number to add, negative to subtract</small>
                                </div>
                                <div class="form-group">
                                    <label>Reason <span class="text-danger">*</span></label>
                                    <textarea name="reason" class="form-control" rows="2"
                                              placeholder="Reason for adjustment" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Adjust Liability
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Set Buttons -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-bolt"></i> Quick Set Liability</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">Click to quickly set the PAID liability to common amounts:</p>
                            <form action="{{ route('admin.coin-ledger.update-liability') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="category" value="PAID">
                                <input type="hidden" name="target_amount" value="10000000">
                                <input type="hidden" name="reason" value="Quick set: 1 crore coins">
                                <button type="submit" class="btn btn-outline-primary mr-2 mb-2">
                                    1 Crore (1,00,00,000)
                                </button>
                            </form>
                            <form action="{{ route('admin.coin-ledger.update-liability') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="category" value="PAID">
                                <input type="hidden" name="target_amount" value="50000000">
                                <input type="hidden" name="reason" value="Quick set: 5 crore coins">
                                <button type="submit" class="btn btn-primary mr-2 mb-2">
                                    <i class="fas fa-star"></i> 5 Crore (5,00,00,000)
                                </button>
                            </form>
                            <form action="{{ route('admin.coin-ledger.update-liability') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="category" value="PAID">
                                <input type="hidden" name="target_amount" value="100000000">
                                <input type="hidden" name="reason" value="Quick set: 10 crore coins">
                                <button type="submit" class="btn btn-outline-primary mr-2 mb-2">
                                    10 Crore (10,00,00,000)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Wallet Ledger -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-history"></i> System Wallet Ledger History</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Entry Type</th>
                                            <th>Category</th>
                                            <th>In</th>
                                            <th>Out</th>
                                            <th>Reference</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($systemLedger as $entry)
                                            <tr>
                                                <td>{{ $entry->id }}</td>
                                                <td>{{ $entry->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    @if($entry->entry_type === 'PAID_COIN_CREDIT')
                                                        <span class="badge badge-success">CREDIT</span>
                                                    @elseif($entry->entry_type === 'REWARD_COIN_CREDIT')
                                                        <span class="badge badge-info">REWARD CREDIT</span>
                                                    @elseif($entry->entry_type === 'COIN_REVERSAL')
                                                        <span class="badge badge-warning">REVERSAL</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $entry->entry_type }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($entry->coin_category === 'PAID')
                                                        <span class="badge badge-success">PAID</span>
                                                    @else
                                                        <span class="badge badge-warning">REWARD</span>
                                                    @endif
                                                </td>
                                                <td class="text-success">
                                                    @if($entry->coins_in > 0)
                                                        +{{ number_format($entry->coins_in) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-danger">
                                                    @if($entry->coins_out > 0)
                                                        -{{ number_format($entry->coins_out) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td><code>{{ $entry->reference_id ?? '-' }}</code></td>
                                                <td>
                                                    @if($entry->metadata && isset($entry->metadata['reason']))
                                                        {{ $entry->metadata['reason'] }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">
                                                    No entries in system wallet yet. Use the forms above to initialize.
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

            <!-- Navigation -->
            <div class="row">
                <div class="col-12">
                    <a href="{{ route('admin.coin-ledger.summary') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Coin Ledger Dashboard
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
