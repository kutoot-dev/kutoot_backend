@extends('admin.master_layout')
@section('title')
<title>{{__('admin.All Coin Campaigns')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.All Coin Campaigns')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.All Coin Campaigns')}}</div>
            </div>
          </div>
@if(Session::has('messege'))
  <script>
      toastr.options = {
          "closeButton": true,
          "progressBar": true
      }
      toastr["{{ Session::get('alert-type', 'info') }}"]("{{ Session::get('messege') }}");
  </script>
@endif

          <div class="section-body">
            <a href="{{ route('admin.create-coin-campaign') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div align="right" class="col-12">
                          <select class="form-control col-sm-12 col-md-6 col-lg-2" id="campaign_types" name="type">
                            @foreach ($types as $key => $type)
                              <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                  <th>{{__('Id')}}</th>
                                    <th>{{__('coin_campaign.title')}}</th>
                                    <th>{{__('coin_campaign.campaign_type')}}</th>
                                    <th>{{__('Marketing Goal Status')}}</th> 
                                    <th>{{__('Actual Status')}}</th>
                                    <!-- <th>{{__('coin_campaign.ticket_price')}}</th> -->
                                    <th>{{__('coin_campaign.img')}}</th>
                                    <th>{{__('Total Tickets')}}</th>
                                    <!-- <th>{{__('coin_campaign.coins_per_campaign')}}</th> -->
                                    <!-- <th>{{__('Total Coupons')}}</th> -->
                                    
                                    <th>{{__('Category')}}</th>
                                    <th>{{__('coin_campaign.start_date')}}</th>
                                    <th>{{__('coin_campaign.end_date')}}</th>
                                    <th>{{__('coin_campaign.status')}}</th>
                                    <th width="14%">{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @php
                                  $keys = array_keys($data);
                                @endphp
                                @foreach ($keys as $campaignType)
                                  @if ($data[$campaignType]->isEmpty())
                                    @continue
                                  @endif
                                  @foreach ($data[$campaignType] as $key => $campaign)
                                      <tr>
                                        <td>{{ 'CP' . str_pad($campaign->id, 3, '0', STR_PAD_LEFT) }}</td>

                                          <td>{{ $campaign->title }}</td>
                                          <td>{{ ucwords($campaignType) }}</td>
                                          <td>{{ $campaign->
                                          marketingManifest()['display_percentage'] }}%</td>
                                          <td>{{ $campaign->
                                          marketingManifest()['progress'] }}%</td>
                                          <!-- <td>{{ $campaign->ticket_price }}</td> -->
                                          <td>
                                              @if ($campaign->img)
                                                  <img src="{{ $campaign->img }}" alt="{{ $campaign->title }}" class="admin-img">
                                              @endif
                                          </td>
                                          <td>{{ $campaign->total_tickets }}</td>
                                          <!-- <td>{{ $campaign->coins_per_campaign }}</td> -->
                                          <!-- <td>{{ $campaign->coupons_per_campaign }}</td> -->
                                          <!-- <td>{{ $campaign->max_coins_per_transaction }}</td> -->
                                          <td>{{ $campaign->category }} </td>
                                          <td>{{ $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date)->format('d M Y') : '-' }}</td>
                                          <td>{{ $campaign->end_date ? \Carbon\Carbon::parse($campaign->end_date)->format('d M Y') : '-' }}</td>
                                          <td>
                                            {{-- @if (strtolower($campaignType) !== 'completed') --}}
                                              @if($campaign->status == 1)
                                                <a href="javascript:;" onclick="changeCoinCampaignStatus({{ $campaign->id }})">
                                                    <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                                </a>
                                              @else
                                                <a href="javascript:;" onclick="changeCoinCampaignStatus({{ $campaign->id }})">
                                                    <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Active')}}" data-off="{{__('admin.Inactive')}}" data-onstyle="success" data-offstyle="danger">
                                                </a>
                                              @endif
                                            {{-- @endif --}}
                                          </td>
                                          <td>
                                              <a href="{{ route('admin.view-coin-campaign', $campaign->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                                              @if (strtolower($campaignType) !== 'completed')
                                                <a href="{{ route('admin.edit-coin-campaign', $campaign->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                               <!--  <form action="{{ route('admin.delete-coin-campaign', $campaign->id) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                </form> -->
                                              @endif

                                               <a href="{{ route('admin.winners.create', [$campaign->id]) }}" class="btn btn-success btn-sm"><i class="fas fa-award"></i>Winner</a>
                                          </td>
                                      </tr>
                                  @endforeach
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

                // Re-initialize toggle buttons after redraw
    $('#dataTable').on('draw.dt', function () {
        $('input[data-toggle="toggle"]').bootstrapToggle();
    });

    // Initial run for page 1
    $('input[data-toggle="toggle"]').bootstrapToggle();
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
