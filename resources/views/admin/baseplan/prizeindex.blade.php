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
           <!-- <a href="{{ route('admin.create-coin-campaign') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('Add New Winner')}}</a> -->
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
                                    <th> Title</th>
                                    <th>Image</th>
                                    <th>{{__('coin_campaign.status')}}</th>
                                    <th>{{__('Created At')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                              
                              
                                  @foreach ($data as $key => $campaign)
                                      <tr>
                                        <td>{{ $campaign->id }}</td>
                                       
                                          <td>{{ $campaign->title }}</td>
                                         
                                        
                                          <td>{{ $campaign->image }}</td>
                                          <td>{{ $campaign->status }}</td>
                                          <td>{{ $campaign->created_at }}</td>
                                          <td><a href="" class="btn"> Details</a></td>
                                        
                                          
                                         
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
