@extends('admin.master_layout')
@section('title')
<title>{{__('Create base Plan')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Create base Plan')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.all-coin-campaigns') }}">{{__('Base Plans')}}</a></div>
              <div class="breadcrumb-item">{{__('Create base Plan')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.store-baseplans') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Thumbnail Image Preview')}}</label>
                                    <div>
                                        <img id="preview-img" class="admin-img" src="{{ asset('uploads/website-images/preview.png') }}" alt="">
                                    </div>
                                </div>

                        


                                <div class="form-group col-12">
                                    <label>{{__('admin.Thumbnail Image')}} <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file"  name="img" onchange="previewThumnailImage(event)">
                                </div>


                                <div class="form-group col-12">
                                    <label>Please select campaign</label>
                                    <select name="camp_id_list[]" multiple class="form-control" style="min-height: 120px;">

                                        @foreach($campaigns as $campaign)
                                            <option value="{{ $campaign->id }}">
                                                {{ $campaign->title }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
              
                                <div class="form-group col-12">
                                    <label>{{__('admin.Title')}} <span class="text-danger">*</span></label>
                                    <input type="text" id="title" class="form-control"  name="title" value="{{ old('title') }}">
                                </div>

                                <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('Plan Price')}} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" id="ticket_price" class="form-control"  name="ticket_price" value="{{ old('ticket_price') }}" required>
                                </div>

                                   {{-- <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('coin_campaign.total_tickets')}} <span class="text-danger">*</span></label>
                                    <input type="number" step="1" id="total_tickets" class="form-control"  name="total_tickets" value="{{ old('$data->total_tickets') }}" required>
                                </div> --}}

                                <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('coin_campaign.coins_per_campaign')}} <span class="text-danger">*</span></label>
                                    <input type="number" step="1" id="coins_per_campaign" class="form-control"  name="coins_per_campaign" value="{{ old('coins_per_campaign') }}" required>
                                </div>

                                <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('coin_campaign.coupons_per_campaign')}} <span class="text-danger">*</span></label>
                                    <input type="number" step="1" id="coupons_per_campaign" class="form-control"  name="coupons_per_campaign" value="{{ old('coupons_per_campaign') }}" required>
                                </div>

                               

                                <div class="form-group col-sm-6 col-md-4">
                                    <label>{{__('admin.Status')}}</label>
                                    <select name="status" class="form-control">
                                        <option value="1">{{__('admin.Active')}}</option>
                                        <option value="0">{{__('admin.Inactive')}}</option>
                                    </select>
                                </div>

                                

                                {{-- <div class="form-group col-6">
                                    <label>{{__('Coin Expire Duration(From Date of Purchase)')}} <span class="text-danger">*</span></label>
                                    <select name="coin_expire" class="form-control">
                                        <option value="3">3 Month</option>
                                        <option value="6">6 Month</option>
                                        <option value="9">9 Month</option>
                                        <option value="12">12 Month</option>
                                        <option value="0">Unlimited</option>
                                       
                                    </select>
                                </div> --}}

                                  <div class="form-group col-4">
                                    <label>{{__('Duration')}} <span class="text-danger">*</span></label>
                                    <input type="text"  class="form-control"  name="duration" placeholder="Validity ends at (YYYY-MM-DD)" value="{{ old('duration') }}">
                                </div>


                                <div class="form-group col-12">
                                    <label>{{__('Point 1')}} </label>
                                    <input type="text"  class="form-control"  name="point1" >
                                </div>


                                <div class="form-group col-12">
                                    <label>{{__('Point 2')}} </label>
                                    <input type="text"  class="form-control"  name="point2" >
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('Favorite Point')}} </label>
                                    <input type="text"  class="form-control"  name="point3" >
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('Point 4')}} </label>
                                    <input type="text"  class="form-control"  name="point4">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('Point 5')}} </label>
                                    <input type="text"  class="form-control"  name="point5" value="{{ old('title') }}">
                                </div>

                                <div class="form-group col-12">
                                    <label>{{__('admin.Description')}}</label>
                                    <textarea name="description" id="" cols="30" rows="10" class="summernote">{{ old('description') }}</textarea>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('admin.Save')}}</button>
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
      </script>
@endsection
