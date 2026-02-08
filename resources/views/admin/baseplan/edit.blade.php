@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Edit Coin Campaign')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Edit Coin Campaign')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.all-coin-campaigns') }}">{{__('admin.All Coin Campaigns')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Edit Coin Campaign')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.update-baseplans', $data->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
    <div class="form-group col-12">
    <label>{{ __('admin.Thumbnail Image Preview') }}</label>
    <div>
        <img id="preview-img" class="admin-img"
             src="{{ asset($data->img ?? 'uploads/website-images/preview.png') }}"
             alt="Thumbnail Preview"
             style="max-width: 200px; height: auto;">
    </div>
</div>

<div class="form-group col-12">
    <label>{{ __('admin.Thumbnail Image') }} <span class="text-danger">*</span></label>
    <input type="file" class="form-control-file" name="img" onchange="previewThumnailImage(event)">
</div>

                                <div class="form-group col-12">
                                    <label>Please select campaign</label>
                                    <select name="camp_id_list[]" multiple class="form-control" style="min-height: 120px;">

                                        @foreach($campaigns as $campaign)
            <option value="{{ $campaign->id }}"
                {{ in_array($campaign->id, $selectedCampaigns ?? []) ? 'selected' : '' }} class="selected">
                {{ $campaign->title }}
            </option>
        @endforeach

                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="title" class="form-control"  name="title" value="{{ $data->title }}">
                                </div>

                                <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('Plan Price')}} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" id="ticket_price" class="form-control"  name="ticket_price" value="{{ $data->ticket_price }}" required>
                                </div>

                                {{-- <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('coin_campaign.total_tickets')}} <span class="text-danger">*</span></label>
                                    <input type="number" step="1" id="total_tickets" class="form-control"  name="total_tickets" value="{{ $data->total_tickets }}" required>
                                </div> --}}

                                <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('coin_campaign.coins_per_campaign')}} <span class="text-danger">*</span></label>
                                    <input type="number" step="1" id="coins_per_campaign" class="form-control"  name="coins_per_campaign" value="{{ $data->coins_per_campaign }}" required>
                                </div>

                                <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('coin_campaign.coupons_per_campaign')}} <span class="text-danger">*</span></label>
                                    <input type="number" step="1" id="coupons_per_campaign" class="form-control"  name="coupons_per_campaign" value="{{ $data->coupons_per_campaign }}" required>
                                </div>
                                  <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('admin.Status')}}</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>{{__('admin.Active')}}</option>
                                        <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>{{__('admin.Inactive')}}</option>
                                    </select>
                                </div>
                                <!-- <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('coin_campaign.max_coins_per_transaction')}} (%) <span class="text-danger">*</span></label>
                                    <input type="number" step="1" min="0" max="100" id="max_coins_per_transaction" class="form-control"  name="max_coins_per_transaction" value="{{ $data->max_coins_per_transaction }}" required>
                                </div> -->

                                <div class="form-group col-4">
                                    <label>{{__('Duration')}} <span class="text-danger">*</span></label>
                                    <input type="text"  class="form-control"  name="duration"  value="{{ $data->duration }}">
                                </div>
                                 <div class="form-group col-12">
                                    <label>{{__('Point 1')}} </label>
                                    <input type="text"  class="form-control"  name="point1" value="{{$data->point1}}">
                                </div>


                                <div class="form-group col-12">
                                    <label>{{__('Point 2')}} </label>
                                    <input type="text"  class="form-control"  name="point2" value="{{ $data->point2 }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('Favrorite Point')}} </label>
                                    <input type="text"  class="form-control"  name="point3" value="{{$data->point3}}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('Point 4')}} </label>
                                    <input type="text"  class="form-control"  name="point4" value="{{ $data->point4 }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('Point 5')}} </label>
                                    <input type="text"  class="form-control"  name="point5" value="{{ $data->point5 }}">
                                </div>

                                <div class="form-group col-12" id="referral_form_url_field" style="display:none;">
                                    <label>{{__('Referral Form URL')}} <small class="text-muted">(For free plans only)</small></label>
                                    <input type="url" class="form-control" name="referral_form_url" value="{{ $data->referral_form_url }}" placeholder="https://forms.google.com/...">
                                </div>

                                <div class="form-group col-12" id="task_form_url_field" style="display:none;">
                                    <label>{{__('Task Form URL')}} <small class="text-muted">(For free plans only)</small></label>
                                    <input type="url" class="form-control" name="task_form_url" value="{{ $data->task_form_url }}" placeholder="https://forms.google.com/...">
                                </div>



                                 <!-- <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('Promotion Type')}}</label>
                                    <select name="promotion" class="form-control">
                                        <option value="Featured">{{__('Featured')}}</option>
                                        <option value="Top-Banner">{{__('Top-Banner')}}</option>
                                    </select>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label>{{__('coin_campaign.start_date')}} <span class="text-danger">*</span></label>
                                    <input type="date" id="start_date" class="form-control"  name="start_date" value="{{ date('Y-m-d', strtotime($data->start_date)) }}" required>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label>{{__('coin_campaign.end_date')}}</label>
                                    <input type="date" id="end_date" class="form-control"  name="end_date" value="{{ $data->end_date ? date('Y-m-d', strtotime($data->end_date)) : null }}">
                                </div>
 -->
                                <div class="form-group col-12">
                                    <label>{{__('admin.Description')}}</label>
                                    <textarea name="description" id="" cols="30" rows="10" class="summernote">{{ $data->description }}</textarea>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('admin.Update')}}</button>
                                </div>
                            </div>
                        </form>
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

        // Show/hide form URL fields based on ticket price
        $(document).ready(function() {
            function toggleFormUrlFields() {
                var price = parseFloat($('#ticket_price').val()) || 0;
                if (price === 0) {
                    $('#referral_form_url_field').slideDown();
                    $('#task_form_url_field').slideDown();
                } else {
                    $('#referral_form_url_field').slideUp();
                    $('#task_form_url_field').slideUp();
                }
            }

            $('#ticket_price').on('input change', toggleFormUrlFields);
            toggleFormUrlFields(); // Initial check
        });
      </script>
@endsection

