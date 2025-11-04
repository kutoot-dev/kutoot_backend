@extends('admin.master_layout')
@section('title')
<title>{{__('admin.All Coin Campaigns')}}</title>
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
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Details</th>
                                    <th>{{__('coin_campaign.title')}}</th>
                                    <th>{{__('coin_campaign.ticket_price')}}</th>
                                     <th>Quantity</th>
                                     <th>Total Price</th>
                                    <th>{{__('coin_campaign.status')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                              
                              
                             @if($data->count())
    @foreach ($data as $key => $campaign)
        <tr>
            <td>{{ $campaign->id }}</td>
            <td>
                Name : {{ $campaign->user->name ?? ''}}<br>
                Email : {{ $campaign->user->email ?? '' }}<br>
                Phone: {{ $campaign->user->phone ?? ''}}<br>
                User ID: {{ $campaign->user->id ?? '' }}
            </td>
            <td>{{ $campaign->camp_title }}</td>
            <td>{{ $campaign->camp_ticket_price }}</td>
            <td>{{ $campaign->quantity }}</td>
            <td>{{ $campaign->quantity * $campaign->camp_ticket_price }}</td>
            <td>{{ $campaign->status }}</td>
            <td>{{ $campaign->created_at }}</td>
            <td>
                <a href="{{ route('admin.purchasedetails', $campaign->id) }}" class="btn btn-primary">Details</a>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="9" class="text-center">No data available.</td>
    </tr>
@endif

                        
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
