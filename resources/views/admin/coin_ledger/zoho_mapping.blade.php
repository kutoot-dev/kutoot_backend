@extends('admin.master_layout')

@section('title')
<title>{{__('Zoho Account Mapping Guide')}}</title>
@endsection

@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><i class="fas fa-book"></i> {{__('Zoho Account Mapping Guide')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.coin-ledger.summary') }}">{{__('Coin Ledger')}}</a></div>
                <div class="breadcrumb-item">{{__('Zoho Mapping')}}</div>
            </div>
        </div>

        <div class="section-body">
            <!-- Overview -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-info-circle"></i> Overview</h4>
                </div>
                <div class="card-body">
                    <p class="lead">
                        This guide explains how coin ledger entries map to Zoho Books accounts for proper accounting treatment.
                    </p>
                    <div class="alert alert-info">
                        <strong>Golden Rule:</strong> The Coin Ledger is the single source of truth.
                        Zoho simply reads from it — it does NOT control the logic.
                    </div>
                </div>
            </div>

            <!-- Mapping Table -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-table"></i> Account Mapping Reference</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Entry Type</th>
                                    <th>Coin Category</th>
                                    <th>Zoho Account</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mappings as $mapping)
                                <tr>
                                    <td>
                                        <code>{{ $mapping['entry_type'] }}</code>
                                    </td>
                                    <td>
                                        @if($mapping['coin_category'] == 'PAID')
                                            <span class="badge badge-success">PAID</span>
                                        @elseif($mapping['coin_category'] == 'REWARD')
                                            <span class="badge badge-warning">REWARD</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $mapping['coin_category'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $mapping['zoho_account'] }}</strong>
                                    </td>
                                    <td>{{ $mapping['description'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Detailed Explanations -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4 class="text-white"><i class="fas fa-money-bill-wave"></i> PAID Coins Flow</h4>
                        </div>
                        <div class="card-body">
                            <ol>
                                <li class="mb-3">
                                    <strong>User Buys Coins</strong><br>
                                    <small class="text-muted">
                                        Bank DR → Cash received<br>
                                        Coin Liability CR → We owe user these coins
                                    </small>
                                </li>
                                <li class="mb-3">
                                    <strong>User Redeems Coins</strong><br>
                                    <small class="text-muted">
                                        Coin Liability DR → Liability reduced<br>
                                        Discount Expense CR → Recognized as discount given
                                    </small>
                                </li>
                                <li>
                                    <strong>Coins Expire Unused</strong><br>
                                    <small class="text-muted">
                                        Coin Liability DR → Liability reduced<br>
                                        Other Income CR → Write-off becomes income
                                    </small>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h4><i class="fas fa-gift"></i> REWARD Coins Flow</h4>
                        </div>
                        <div class="card-body">
                            <ol>
                                <li class="mb-3">
                                    <strong>User Gets Bonus Coins</strong><br>
                                    <small class="text-muted">
                                        Marketing Expense DR → Cost of promotion<br>
                                        Marketing Liability CR → We owe user these coins
                                    </small>
                                </li>
                                <li class="mb-3">
                                    <strong>User Redeems Reward Coins</strong><br>
                                    <small class="text-muted">
                                        Marketing Liability DR → Liability reduced<br>
                                        Marketing Expense CR → Expense realized
                                    </small>
                                </li>
                                <li>
                                    <strong>Reward Coins Expire</strong><br>
                                    <small class="text-muted">
                                        Marketing Liability DR → Liability reduced<br>
                                        Marketing Expense Reversal CR → Expense not realized
                                    </small>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Instructions -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-file-export"></i> How to Export for Zoho</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Step 1: Export Data</h5>
                            <p>
                                Go to <a href="{{ route('admin.coin-ledger.index') }}">Transactions</a> and click
                                <strong>"Export Results"</strong> to download an Excel file.
                            </p>
                            <p>
                                The export includes a <code>Zoho Account Type</code> column that automatically
                                classifies each transaction.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Step 2: Import to Zoho</h5>
                            <p>
                                Use the <code>Zoho Account Type</code> column to:
                            </p>
                            <ul>
                                <li>Filter by account type</li>
                                <li>Create journal entries in Zoho Books</li>
                                <li>Reconcile liability accounts</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <strong>Important:</strong> Always reconcile the exported data against your bank statements
                        and payment gateway reports before creating Zoho entries.
                    </div>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-check-circle"></i> Summary</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="p-3 border rounded text-center">
                                <h5 class="text-success">Money comes in</h5>
                                <p class="mb-0">Tag as <strong>PAID</strong> coin</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded text-center">
                                <h5 class="text-warning">Free coins given</h5>
                                <p class="mb-0">Tag as <strong>REWARD</strong> coin</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded text-center">
                                <h5 class="text-info">Coins used</h5>
                                <p class="mb-0">Debit based on <strong>tag</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <p class="lead text-muted">
                            Wallet = Computed view only (no separate balance tables)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
