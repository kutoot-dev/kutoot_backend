@extends('store.master_layout')

@section('store-content')
<section class="section">
    <div class="section-header">
        <h1>Bank Details</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('store.settings.bank.update') }}">
                @csrf

                <div class="form-group">
                    <label>Bank Name</label>
                    <input class="form-control" name="bank_name" value="{{ old('bank_name', $bank?->bank_name) }}" required>
                </div>

                <div class="form-group">
                    <label>Account Number</label>
                    <input class="form-control" name="account_number" value="{{ old('account_number', $bank?->account_number) }}" required>
                </div>

                <div class="form-group">
                    <label>IFSC</label>
                    <input class="form-control" name="ifsc" value="{{ old('ifsc', $bank?->ifsc) }}" required>
                    <small class="text-muted">Format: HDFC0XXXXXX</small>
                </div>

                <div class="form-group">
                    <label>UPI ID</label>
                    <input class="form-control" name="upi_id" value="{{ old('upi_id', $bank?->upi_id) }}">
                </div>

                <div class="form-group">
                    <label>Beneficiary Name</label>
                    <input class="form-control" name="beneficiary_name" value="{{ old('beneficiary_name', $bank?->beneficiary_name) }}" required>
                </div>

                <button class="btn btn-primary" type="submit">Save</button>
            </form>
        </div>
    </div>
</section>
@endsection


