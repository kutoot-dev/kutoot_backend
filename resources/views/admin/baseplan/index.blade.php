@extends('admin.master_layout')
@section('title')
<title>{{__('All Base Plans')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('All Base Plans')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('All Base Plans')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.create-baseplans') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">

                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                  <th>ID</th>
                                    <th>{{__('coin_campaign.title')}}</th>
                                    <th>{{__('Campaign Name')}}</th>
                                    <th>{{__('Price')}}</th>
                                    <th>{{__('coin_campaign.img')}}</th>

                                    <th>{{__('Total Earning Coins')}}</th>
                                    <th>{{__('Total Coupons')}}</th>


                                    <th>{{__('coin_campaign.status')}}</th>
                                    <th width="14%">{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>


                                  @foreach ($data as $key => $campaign)
                                      <tr>
                                        <td>{{ $campaign->id }}</td>
                                          <td>{{ $campaign->title }}</td>
                                          <td>
                                              @if($campaign->campaigns && $campaign->campaigns->count() > 0)
                                                  @foreach($campaign->campaigns as $campaignItem)
                                                      <span class="badge badge-primary mb-2">{{ $campaignItem->title }}</span>
                                                  @endforeach
                                              @else
                                                  <span class="text-muted">-</span>
                                              @endif
                                          </td>

                                          <td>{{ $campaign->ticket_price }}</td>
                                          <td>
                                              @if ($campaign->img)
                                                  <img src="{{ $campaign->img }}" alt="{{ $campaign->title }}" class="admin-img">
                                              @endif
                                          </td>

                                          <td>{{ $campaign->coins_per_campaign }}</td>
                                          <td>{{ $campaign->coupons_per_campaign }}</td>

                                          <td>

                                              @if($campaign->status == 1)
                                                <a href="javascript:;" onclick="changeCoinCampaignStatus({{ $campaign->id }})">
                                                    <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                                </a>
                                              @else
                                                <a href="javascript:;" onclick="changeCoinCampaignStatus({{ $campaign->id }})">
                                                    <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                                </a>
                                              @endif

                                          </td>


                                          <td>
                                              <a href="{{ route('admin.view-baseplans', $campaign->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>

                                                <a href="{{ route('admin.edit-baseplans', $campaign->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                               <!--  <form action="{{ route('admin.delete-coin-campaign', $campaign->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                </form> -->



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
                url:"{{url('/admin/base-campaign-status/')}}"+"/"+id,
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
