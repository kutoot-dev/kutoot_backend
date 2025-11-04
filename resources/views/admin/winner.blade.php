@extends('admin.master_layout')
@section('title')
<title>{{__('admin.All Coin Campaigns')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('All Winner List')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('All Winner List')}}</div>
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
                                    <th> ID</th>
                                    <th>Image</th>
                                    <th> Campaign Name</th>
                                    <th> Coupon Code</th>
                                    <th> Announced Date</th>
                                    <th> Winner Details</th>
                                    <th> Is Claimed</th>
                                    <th>Actions</th>
                                  </tr>
                            </thead>
                            <tbody>
                              
                              
                                  @foreach ($winners as $key => $campaign)
                                      <tr>
                                         <td>{{ $campaign->id }}</td>
                                        <td>  @if ($campaign->campaign)
                                                  <img src="{{$campaign->campaign?->campaign?->img }}" width="100px" alt="{{ $campaign->title }}">
                                              @endif</td>
                                       

                                        <td>{{ $campaign->campaign?->campaign?->title }}</td>
                                         <td>{{ $campaign->coupon_number }}</td>
                                         <td>{{ $campaign->announcing_date }}</td>
                                        <td>{{ $campaign->user->name ?? '' }}<br>
                                            {{ $campaign->user->email ?? '' }}<br>
                                            {{ $campaign->user->phone  ?? ''}}</td>

                                        <td>{{ $campaign->is_claimed }}</td>
                                       <td> 

                                     <form action="{{ route('admin.winner.delete', [$campaign->id]) }}" method="POST" style="display:inline-block;" 
      onsubmit="return confirm('Are you sure you want to cancel the draw?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm">Cancel Draw</button>
</form>


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
