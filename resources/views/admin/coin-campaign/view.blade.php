@extends('admin.master_layout')
@section('title')
<title>{{__('admin.View Coin Campaign')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.View Coin Campaign')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.all-coin-campaigns') }}">{{__('admin.All Coin Campaigns')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.View Coin Campaign')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                          <div class="row">
                            <table class="table table-striped col-sm-12 col-md-8 col-lg-8" align="center">
                              <tr>
                                <th>{{__('admin.Thumbnail Image')}}</th>
                                 <th>{{__('Image 1')}}</th>
                                <th>{{__('Image 2')}}</th>
                                <th>{{__('Video')}}</th>
                              </tr>
                              <tr>
                                    <td>
                                  @if ($data->img)
                                    <img id="preview-img" class="admin-img" src={{ $data->img }} alt="">
                                  @endif   
                                  </td>
                                   <td>
                                  @if ($data->image1)
                                    <img id="image1" class="admin-img" src={{ asset($data['image1']) }} alt="">
                                  @endif
                                </td>  
                                 <td>
                                  @if ($data->image2)
                                    <img id="image2" class="admin-img" src={{ asset($data['image2']) }} alt="">
                                  @endif
                                </td>  
                                <td>
                                  @if ($data->video)
                                    <video width="320" height="240" controls>
                                      <source src="{{ asset($data->video) }}" type="video/mp4">
                                      Your browser does not support the video tag.
                                    </video>
                                  @endif
                                </td>
                                </tr>

                                   <tr>
                                <th>{{__('Campaign ID')}}</th>
                                <td>{{ $data->campaign_id ?? '' }}</td>
                              </tr>
                              <tr>
                                <th>{{__('admin.Title')}}</th>
                                <td>{{ $data->title ?? '' }}</td>
                              </tr>
                                 <tr>
                                <th>{{__('Title1')}}</th>
                                <td>{{ $data->title1 ?? '' }}</td>
                              </tr>
                                 <tr>
                                <th>{{__('Title2')}}</th>
                                <td>{{ $data->title2 ?? '' }}</td>
                              </tr>
                               <tr>
                                <th>{{__('Short Description')}}</th>
                                <td>{{ $data->short_description ?? '' }}</td>
                              </tr>
                              <tr>
                                <th>{{__('coin_campaign.ticket_price')}}</th>
                                <td>{{ $data->ticket_price  ?? ''}}</td>
                              </tr>
                                <tr>
                                <th>{{__('Series Prefix')}}</th>
                                <td>{{ $data->series_prefix ?? ''}}</td>
                              </tr>
                                <tr>
                                <th>{{__('Number Min')}}</th>
                                <td>{{ $data->number_min  ?? ''}}</td>
                              </tr>
                              <tr>
                              <tr>
                                <th>{{__('Number Max')}}</th>
                                <td>{{ $data->number_max ?? ''}}</td>
                              </tr>
                                <tr>
                                <th>{{__('Numbers Per Ticket')}}</th>
                                <td>{{ $data->numbers_per_ticket ?? ''}}</td>
                              </tr>
                                <tr>
                                <th>{{__('Max Length')}}</th>
                                <td>{!! $data->max_length ?? '' !!}</td>
                              </tr>
                              <tr>
                                <th>{{__('coin_campaign.total_tickets')}}</th>
                                <td>{{ $data->total_tickets ?? '' }}</td>
                              </tr>
                              <tr>
                                <th>{{__('coin_campaign.coins_per_campaign')}}</th>
                                <td>{{ $data->coins_per_campaign ?? '' }}</td>
                              </tr>
                              <tr>
                                <th>{{__('coin_campaign.coupons_per_campaign')}}</th>
                                <td>{{ $data->coupons_per_campaign ?? ''}}</td>
                              </tr>
                              <tr>
                                <th>{{__('coin_campaign.max_coins_per_transaction')}}</th>
                                <td>{{ $data->max_coins_per_transaction ?? '' }}</td>
                              </tr>
                              <tr>
                                <th>{{__('admin.Status')}}</th>
                                <td>{{ $data->status == 1 ? __('admin.Active') : __('admin.Inactive') }}</td>
                              </tr>
                              <tr>
                                <th>{{__('coin_campaign.start_date')}}</th>
                                <td>{{ $data->start_date ? date('d/m/Y', strtotime($data->start_date)) : null }}</td>
                              </tr>
                              <tr>
                                <th>{{__('coin_campaign.end_date')}}</th>
                                <td>{{ $data->end_date ? date('d/m/Y', strtotime($data->end_date)) : null }}</td>
                              </tr>
                              <tr>
                                <th>{{ "Tag 1" }} </th>
                                 <th>{{ "Tag 2" }} </th>
                              </tr>
                                  <tr>
                                <td>{{ $data->tag1 ?? ''}}</td>
                                
                                <td>{{ $data->tag2 ?? '' }}</td>
                              </tr>

                              <tr>
                                <th>{{__('admin.Description')}}</th>
                                <td>{!! $data->description !!}</td>
                              </tr>
                              <tr>
                                <tr>
                                  <th>{{ _('Winnner Announcement Date') }} </th>
                                  <td>{{ $data->winner_announcement_date ? date('d/m/Y', strtotime($data->winner_announcement_date)) : null }}</td>
                                </tr>
                              

                                <tr>
    <th>{{ __('Highlights') }}</th>
    <td>
        @if(!empty($data->highlights) && is_array($data->highlights))
            <table class="table table-bordered table-sm">
                <tbody>
                @foreach($data->highlights as $index => $object)
                    <tr>
                        <td colspan="2"><strong>Highlight {{ $index + 1 }}</strong></td>
                    </tr>
                    @foreach($object as $key => $value)
                        <tr>
                            <th style="width: 30%">{{ $key }}</th>
                            <td>{{ $value }}</td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        @else
            <em>No highlights available</em>
        @endif
    </td>
</tr>

                              <tr>
                              
                                <td colspan="2" class="text-right">
                                  <a href="{{ route('admin.all-coin-campaigns') }}" class="btn btn-primary">{{__('admin.Back')}}</a>
                                </td>
                              </tr>
                            </table>
                          </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>
      <script>
        function previewThumnailImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('preview-img');
                output.src = reader.result;
            }

            reader.readAsDataURL(event.target.files[0]);
        };

      </script>
@endsection
