@extends('admin.master_layout')
@section('title')
<title>Edit Application - {{ $application->application_id }}</title>
@endsection
@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Edit Store Application</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.seller-applications.index') }}">Store Applications</a></div>
                <div class="breadcrumb-item">Edit {{ $application->application_id }}</div>
            </div>
        </div>

        <div class="section-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible show fade">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert"><span>&times;</span></button>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible show fade">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert"><span>&times;</span></button>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Application: {{ $application->application_id }}</h4>
                            <div class="card-header-action">
                                @switch($application->status)
                                    @case('PENDING')
                                        <span class="badge badge-warning badge-lg">Pending</span>
                                        @break
                                    @case('VERIFIED')
                                        <span class="badge badge-info badge-lg">Verified</span>
                                        @break
                                    @case('REJECTED')
                                        <span class="badge badge-danger badge-lg">Rejected</span>
                                        @break
                                @endswitch
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.seller-applications.update', $application->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Store Information</h5>

                                        <div class="form-group">
                                            <label>Store Name <span class="text-danger">*</span></label>
                                            <input type="text" name="store_name" class="form-control" required value="{{ old('store_name', $application->store_name) }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Store Type <span class="text-danger">*</span></label>
                                            <input type="text" name="store_type" class="form-control" required value="{{ old('store_type', $application->store_type) }}" placeholder="e.g., Restaurant, Retail, Salon">
                                        </div>

                                        <div class="form-group">
                                            <label>Store Address <span class="text-danger">*</span></label>
                                            <textarea name="store_address" class="form-control" rows="3" required>{{ old('store_address', $application->store_address) }}</textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Minimum Bill Amount</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">₹</span>
                                                </div>
                                                <input type="number" name="min_bill_amount" class="form-control" min="0" step="0.01" value="{{ old('min_bill_amount', $application->min_bill_amount) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h5 class="mb-3">Owner Information</h5>

                                        <div class="form-group">
                                            <label>Owner Mobile <span class="text-danger">*</span></label>
                                            <input type="text" name="owner_mobile" class="form-control" required value="{{ old('owner_mobile', $application->owner_mobile) }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Owner Email</label>
                                            <input type="email" name="owner_email" class="form-control" value="{{ old('owner_email', $application->owner_email) }}">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Location Details</h5>

                                        <div class="form-group">
                                            <label>Country</label>
                                            <select name="country_id" id="country_id" class="form-control select2">
                                                <option value="">{{__('admin.Select a Country')}}</option>
                                                @foreach($countries as $country)
                                                    @php
                                                        $countryId = is_array($country) ? $country['id'] : $country->id;
                                                        $countryName = is_array($country) ? $country['name'] : $country->name;
                                                    @endphp
                                                    <option value="{{ $countryId }}" {{ old('country_id', $application->country_id) == $countryId ? 'selected' : '' }}>{{ $countryName }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>State</label>
                                            <select name="state_id" id="state_id" class="form-control select2">
                                                <option value="">{{__('admin.Select a State')}}</option>
                                                @foreach($states as $state)
                                                    @php
                                                        $stateId = is_array($state) ? $state['id'] : $state->id;
                                                        $stateName = is_array($state) ? $state['name'] : $state->name;
                                                    @endphp
                                                    <option value="{{ $stateId }}" {{ old('state_id', $application->state_id) == $stateId ? 'selected' : '' }}>{{ $stateName }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>City</label>
                                            <select name="city_id" id="city_id" class="form-control select2">
                                                <option value="">{{__('admin.Select a City')}}</option>
                                                @foreach($cities as $city)
                                                    @php
                                                        $cityId = is_array($city) ? $city['id'] : $city->id;
                                                        $cityName = is_array($city) ? $city['name'] : $city->name;
                                                    @endphp
                                                    <option value="{{ $cityId }}" {{ old('city_id', $application->city_id) == $cityId ? 'selected' : '' }}>{{ $cityName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h5 class="mb-3">GPS Coordinates</h5>

                                        <div class="form-group">
                                            <label>Latitude</label>
                                            <input type="number" name="lat" class="form-control" step="0.0000001" value="{{ old('lat', $application->lat) }}" placeholder="e.g., 12.9716">
                                        </div>

                                        <div class="form-group">
                                            <label>Longitude</label>
                                            <input type="number" name="lng" class="form-control" step="0.0000001" value="{{ old('lng', $application->lng) }}" placeholder="e.g., 77.5946">
                                        </div>

                                        @if($application->lat && $application->lng)
                                        <a href="https://www.google.com/maps?q={{ $application->lat }},{{ $application->lng }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-map-marker-alt"></i> View on Google Maps
                                        </a>
                                        @endif
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Shop Settings</h5>

                                        <div class="form-group">
                                            <label>Commission Percent (%)</label>
                                            <div class="input-group">
                                                <input type="number" name="commission_percent" class="form-control" min="0" max="100" step="0.01" value="{{ old('commission_percent', $application->commission_percent) }}" placeholder="e.g., 10">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Discount Percent (%)</label>
                                            <div class="input-group">
                                                <input type="number" name="discount_percent" class="form-control" min="0" max="100" step="0.01" value="{{ old('discount_percent', $application->discount_percent) }}" placeholder="e.g., 5">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Rating (0-5)</label>
                                            <input type="number" name="rating" class="form-control" min="0" max="5" step="0.1" value="{{ old('rating', $application->rating) }}" placeholder="e.g., 4.5">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h5 class="mb-3">Store Image</h5>

                                        <div class="form-group">
                                            <label>Store Image</label>
                                            <input type="file" name="store_image" class="form-control-file" accept="image/*">
                                            <small class="text-muted">Recommended size: 400x400 pixels. Accepted formats: JPG, PNG, GIF</small>
                                        </div>

                                        @if($application->store_image)
                                        <div class="form-group">
                                            <label>Current Image</label><br>
                                            <img src="{{ asset($application->store_image) }}" alt="Store Image" class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save"></i> Save Changes
                                        </button>
                                        <a href="{{ route('admin.seller-applications.show', $application->id) }}" class="btn btn-secondary btn-lg">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    (function($) {
        "use strict";
        $(document).ready(function () {
            // Initialize select2 if available
            if ($.fn.select2) {
                $('.select2').select2();
            }

            // Load states when country is selected
            $("#country_id").on("change", function(){
                var countryId = $(this).val();
                if(countryId){
                    $.ajax({
                        type: "get",
                        url: "{{ url('/admin/state-by-country') }}/" + countryId,
                        success: function(response){
                            var stateHtml = "<option value=''>{{__('admin.Select a State')}}</option>";
                            if(response.states && response.states.length > 0){
                                $.each(response.states, function(index, state){
                                    stateHtml += "<option value='" + state.id + "'>" + state.name + "</option>";
                                });
                            }
                            $("#state_id").html(stateHtml);
                            if ($.fn.select2) {
                                $("#state_id").select2();
                            }
                            // Clear cities
                            var cityHtml = "<option value=''>{{__('admin.Select a City')}}</option>";
                            $("#city_id").html(cityHtml);
                            if ($.fn.select2) {
                                $("#city_id").select2();
                            }
                        },
                        error: function(err){
                            console.log(err);
                        }
                    });
                } else {
                    var stateHtml = "<option value=''>{{__('admin.Select a State')}}</option>";
                    $("#state_id").html(stateHtml);
                    var cityHtml = "<option value=''>{{__('admin.Select a City')}}</option>";
                    $("#city_id").html(cityHtml);
                }
            });

            // Load cities when state is selected
            $("#state_id").on("change", function(){
                var stateId = $(this).val();
                if(stateId){
                    $.ajax({
                        type: "get",
                        url: "{{ url('/admin/city-by-state') }}/" + stateId,
                        success: function(response){
                            var cityHtml = "<option value=''>{{__('admin.Select a City')}}</option>";
                            if(response.cities && response.cities.length > 0){
                                $.each(response.cities, function(index, city){
                                    cityHtml += "<option value='" + city.id + "'>" + city.name + "</option>";
                                });
                            }
                            $("#city_id").html(cityHtml);
                            if ($.fn.select2) {
                                $("#city_id").select2();
                            }
                        },
                        error: function(err){
                            console.log(err);
                        }
                    });
                } else {
                    var cityHtml = "<option value=''>{{__('admin.Select a City')}}</option>";
                    $("#city_id").html(cityHtml);
                }
            });
        });
    })(jQuery);
</script>
@endsection
