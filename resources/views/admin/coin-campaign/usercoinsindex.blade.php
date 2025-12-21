@extends('admin.master_layout')
@section('title')
<title>{{__('User Coin Statements')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('All Purchase Orders')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('All Purchase Orders')}}</div>
            </div>
          </div>

          <div class="section-body">
           
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                    
                      </div>
                      <div class="table-responsive table-invoice">
                        
                        <table class="table table-bordered table-striped" id="dataTable">
                          <thead>
                              <tr>
                                  <th>ID</th>
                                  <th>User Details</th>
                                  <th>Order Details</th>
                                  <th>Coins</th>
                                  <th>Type</th>
                                  <th>Expires At</th>
                                  <th>Status</th>
                                  <th>Created</th>
                                  <th>Campaign Details</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach ($userCoins as $coin)
                                  <tr>
                                      <td>{{ $coin->id }}</td>
                                      <td>Name : {{ $coin->user->name }}<br>
                                          Email : {{ $coin->user->email }}<br>
                                          Phone: {{ $coin->user->phone }}<br>
                                         User ID: {{ $coin->user->id }}</td>
                                      <td>
                                        @if ($coin->orderdetails)
                                              {{ $coin->orderdetails->id ?? 'N/A' }}
                                          @else
                                              N/A
                                          @endif
                                      </td>
                                      <td>{{ $coin->coins }}</td>
                                      <td>{{ $coin->type }}</td>
                                      <td>{{ $coin->created_at->copy()->addDays(100)->format('Y-m-d H:i:s') }}</td>
                                      <td>{{ $coin->status ? 'Active' : 'Inactive' }}</td>
                                      <td>{{ $coin->created_at->format('Y-m-d') }}</td>
                                      <td>
                                          @if ($coin->purchasedCampaign)
                                              {{ $coin->purchasedCampaign->camp_title ?? 'N/A' }} <br>
                                              Price : {{ $coin->purchasedCampaign->camp_ticket_price ?? 'N/A' }} <br>
                                              {{ $coin->purchasedCampaign->created_at ?? 'N/A' }}
                                          @else
                                              N/A
                                          @endif
                                      </td>
                                  </tr>
                              @endforeach
                          </tbody>
                      </table>


                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>
<script>
  $(document).ready(function() {
      const route = "{{ route('admin.all-coin-campaigns') }}";
      $('#campaign_types').change(function(e) {
          e.preventDefault();
          const queryParams = new URLSearchParams(window.location.search);
          const params = Object.fromEntries(queryParams.entries());
          params.type = $(this).val();
          const queryString = new URLSearchParams(params).toString();
          window.location.href = route + '?' + queryString;
      });
      $('#dataTable').DataTable({
          order: [] // Prevents initial sorting but allows manual sorting
      });
  });
  function changeCoinCampaignStatus(id){
      $.ajax({
          type:"put",
          data: { _token : '{{ csrf_token() }}' },
          url:"{{url('/admin/coin-campaign-status/')}}"+"/"+id,
          success:function(response){
              toastr.success(response)
          },
          error:function(err){
              console.log(err);
          }
      })
  }
</script>
@endsection
