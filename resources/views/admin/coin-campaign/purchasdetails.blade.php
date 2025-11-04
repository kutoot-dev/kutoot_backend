@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Edit Coin Campaign')}}</title>
@endsection
@section('admin-content')
 <div class="main-content">
        <section class="section">
          <div class="section-header">
<div class="container mt-4">
    <h2>Purchase Details</h2>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ $purchase->camp_title }}  </h5>
            <h5>Order ID : {{ $purchase->id }} </h5>
            <br>
            <!-- <p><strong>Description:</strong> {{ $purchase->camp_description }}</p> -->
            <p><strong>Ticket Price:</strong> {{ $purchase->camp_ticket_price }}</p>
            <p><strong>Coins Earned:</strong> {{ $purchase->camp_coins_per_campaign }}</p>
            <p><strong>Coupons Count:</strong> {{ $purchase->camp_coupons_per_campaign }}</p>
            <p><strong>Status:</strong> {{ $purchase->status ? 'Active' : 'Inactive' }}</p>
            <p><strong>Quantity:</strong> {{ $purchase->quantity }}</p>
            <p><strong>User:</strong> {{ $purchase->user->name ?? 'N/A' }} (ID: {{ $purchase->user_id }})</p>
            <p><strong>Created At:</strong> {{ $purchase->created_at }}</p>
        </div>
    </div>

    <h4>Coupons List</h4>
    @if($purchase->coupons->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Coupon Code</th>
                    <th>Expires</th>
                    <th>Is Expired</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->coupons as $index => $coupon)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $coupon->coupon_code }}</td>
                    <td>{{ $coupon->coupon_expires }}</td>
                    <td>{{ $coupon->is_claimed ? 'Yes' : 'No' }}</td>
                    <td>{{ $coupon->status ? 'Active' : 'Inactive' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No coupons found for this purchase.</p>
    @endif

    <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>

</div>
</div>
</section>
</div>
@endsection
