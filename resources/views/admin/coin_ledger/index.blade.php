@extends('admin.master_layout')

@section('title')
<title>{{__('Coin Ledger Transactions')}}</title>
@endsection

@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><i class="fas fa-list"></i> {{__('Coin Ledger Transactions')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.coin-ledger.summary') }}">{{__('Coin Ledger')}}</a></div>
                <div class="breadcrumb-item">{{__('Transactions')}}</div>
            </div>
        </div>

        <div class="section-body">
            <!-- Filters Card -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-filter"></i> Filters</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.coin-ledger.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Search User</label>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Name, Email, Phone" 
                                           value="{{ $filters['search'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Entry Type</label>
                                    <select name="entry_type" class="form-control">
                                        <option value="">All Types</option>
                                        @foreach($entryTypes as $value => $label)
                                            <option value="{{ $value }}" {{ ($filters['entry_type'] ?? '') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="coin_category" class="form-control">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $value => $label)
                                            <option value="{{ $value }}" {{ ($filters['coin_category'] ?? '') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" name="date_from" class="form-control" 
                                           value="{{ $filters['date_from'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" name="date_to" class="form-control" 
                                           value="{{ $filters['date_to'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Reference ID</label>
                                    <input type="text" name="reference_id" class="form-control" 
                                           placeholder="Order/Campaign ID" 
                                           value="{{ $filters['reference_id'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                                <a href="{{ route('admin.coin-ledger.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                                <a href="{{ route('admin.coin-ledger.export', request()->query()) }}" class="btn btn-success float-right">
                                    <i class="fas fa-file-excel"></i> Export Results
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card">
                <div class="card-header">
                    <h4>Transactions ({{ $entries->total() }} records)</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Entry Type</th>
                                    <th>Category</th>
                                    <th class="text-right">Coins In</th>
                                    <th class="text-right">Coins Out</th>
                                    <th>Expiry</th>
                                    <th>Reference</th>
                                    <th>Zoho Account</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                <tr>
                                    <td>{{ $entry->id }}</td>
                                    <td>
                                        <small>{{ $entry->created_at->format('Y-m-d') }}</small><br>
                                        <small class="text-muted">{{ $entry->created_at->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        @if($entry->user)
                                            <a href="{{ route('admin.coin-ledger.user', $entry->user_id) }}">
                                                {{ $entry->user->name ?? 'N/A' }}
                                            </a>
                                            <br><small class="text-muted">{{ $entry->user->email ?? '' }}</small>
                                        @else
                                            <span class="text-muted">User #{{ $entry->user_id }}</span>
                                        @endif
                                    </td>
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
                                            <small class="text-muted">No Expiry</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $entry->reference_id ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ \App\Services\CoinLedgerService::getZohoAccountType($entry) }}
                                        </small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        No transactions found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $entries->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
