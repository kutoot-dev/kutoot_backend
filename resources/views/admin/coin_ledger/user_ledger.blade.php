@extends('admin.master_layout')

@section('title')
<title>{{__('User Coin Ledger')}} - {{ $user->name }}</title>
@endsection

@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><i class="fas fa-user"></i> {{__('User Coin Ledger')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.coin-ledger.summary') }}">{{__('Coin Ledger')}}</a></div>
                <div class="breadcrumb-item">{{ $user->name }}</div>
            </div>
        </div>

        <div class="section-body">
            <!-- User Info & Balance Card -->
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-user-circle"></i> User Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if($user->image)
                                    <img src="{{ asset($user->image) }}" class="rounded-circle" width="80" height="80" alt="User">
                                @else
                                    <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <span class="text-white h4 mb-0">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                            </div>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Joined:</strong></td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                </tr>
                            </table>
                            <a href="{{ route('admin.customer-show', $user->id) }}" class="btn btn-sm btn-outline-primary btn-block">
                                View Full Profile
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-primary">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Total Balance</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ number_format($breakdown['total']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-success">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Paid Coins</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ number_format($breakdown['paid']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-warning">
                                    <i class="fas fa-gift"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Reward Coins</h4>
                                    </div>
                                    <div class="card-body">
                                        {{ number_format($breakdown['reward']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Credit Form -->
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="fas fa-plus-circle"></i> Manual Credit</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.coin-ledger.manual-credit') }}" method="POST" class="form-inline">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <div class="form-group mr-2">
                                    <input type="number" name="amount" class="form-control" placeholder="Amount" min="1" required>
                                </div>
                                <div class="form-group mr-2">
                                    <select name="category" class="form-control" required>
                                        <option value="PAID">Paid</option>
                                        <option value="REWARD">Reward</option>
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <input type="text" name="reason" class="form-control" placeholder="Reason" required>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Credit
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-history"></i> Transaction History</h4>
                    <div class="card-header-action">
                        <a href="{{ route('admin.coin-ledger.export', ['user_id' => $user->id]) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Entry Type</th>
                                    <th>Category</th>
                                    <th class="text-right">Coins In</th>
                                    <th class="text-right">Coins Out</th>
                                    <th>Expiry</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                <tr>
                                    <td>{{ $entry->id }}</td>
                                    <td>{{ $entry->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'PAID_COIN_CREDIT' => 'success',
                                                'REWARD_COIN_CREDIT' => 'info',
                                                'COIN_REDEEM' => 'danger',
                                                'COIN_EXPIRE' => 'warning',
                                                'COIN_REVERSAL' => 'secondary',
                                            ];
                                            $color = $typeColors[$entry->entry_type] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $color }}">
                                            {{ $entryTypes[$entry->entry_type] ?? $entry->entry_type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $entry->coin_category == 'PAID' ? 'success' : 'warning' }}">
                                            {{ $entry->coin_category }}
                                        </span>
                                    </td>
                                    <td class="text-right text-success font-weight-bold">
                                        {{ $entry->coins_in > 0 ? '+' . number_format($entry->coins_in) : '-' }}
                                    </td>
                                    <td class="text-right text-danger font-weight-bold">
                                        {{ $entry->coins_out > 0 ? '-' . number_format($entry->coins_out) : '-' }}
                                    </td>
                                    <td>
                                        @if($entry->expiry_date)
                                            <small class="{{ $entry->expiry_date->isPast() ? 'text-danger' : '' }}">
                                                {{ $entry->expiry_date->format('Y-m-d') }}
                                            </small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td><small>{{ $entry->reference_id ?? '-' }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No transactions found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $entries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
