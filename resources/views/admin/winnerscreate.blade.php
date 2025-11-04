@extends('admin.master_layout')
@section('title')
<title>{{__('Announce Winner')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Announce Winner')}}</h1>
           
          </div>
      </section>
 

    <div class="section-body">
            <div class="row mt-4">

                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.winners.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="camp_id">Selected Campaign</label>
            <br>
            <input type="hidden" name="camp_id" value="{$campaigns->id}}">
            <label class="form-control">{{$campaigns->title}}</label>
            
        </div>

        <div class="form-group mt-3">
            <label for="coupon_id">Enter Winner Coupon Code</label>
            <select name="coupon_id" id="coupon_id" class="form-control select2" required>
              
                @foreach($coupons as $id => $coupon)
                    <option value="{{ $coupon->id }}">{{ $coupon->coupon_code }}</option>
                @endforeach
            </select>
        </div>

       <div class="form-group mt-3">
            <label for="announcing_date">Winning Date & Time</label>
            <input type="datetime-local" name="announcing_date" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label for="prize_details">Prize Details</label>
            <textarea name="prize_details" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Announce Winner</button>
    </form>
</div>
</div>
 </div>
</div>
</div>

<script>
    $(document).ready(function() {
        $('#coupon_id').select2({
            placeholder: "Search or select coupon code",
            allowClear: true
        });
    });
</script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery (required by Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection



