@extends('admin.master_layout')
@section('title')
<title>Create Store Application</title>
@endsection
@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Create Store Application</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.seller-applications.index') }}">Store Applications</a></div>
                <div class="breadcrumb-item">Create New</div>
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
                            <h4>New Store Application</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.seller-applications.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Store Information</h5>

                                        <div class="form-group">
                                            <label>Store Name <span class="text-danger">*</span></label>
                                            <input type="text" name="store_name" class="form-control" required value="{{ old('store_name') }}" placeholder="Enter store name">
                                        </div>

                                        <div class="form-group">
                                            <label>Store Type <span class="text-danger">*</span></label>
                                            <input type="text" name="store_type" class="form-control" required value="{{ old('store_type') }}" placeholder="e.g., Restaurant, Retail, Salon">
                                        </div>

                                        <div class="form-group">
                                            <label>Store Address <span class="text-danger">*</span></label>
                                            <textarea name="store_address" class="form-control" rows="3" required placeholder="Enter full store address">{{ old('store_address') }}</textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Minimum Bill Amount</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">₹</span>
                                                </div>
                                                <input type="number" name="min_bill_amount" class="form-control" min="0" step="0.01" value="{{ old('min_bill_amount', 0) }}" placeholder="0.00">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>GST Number</label>
                                            <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number') }}" placeholder="e.g., 22AAAAA0000A1Z5" maxlength="20">
                                            <small class="text-muted">15-character alphanumeric GST identification number</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h5 class="mb-3">Owner Information</h5>

                                        <div class="form-group">
                                            <label>Owner Mobile <span class="text-danger">*</span></label>
                                            <input type="text" name="owner_mobile" class="form-control" required value="{{ old('owner_mobile') }}" placeholder="e.g., 9876543210">
                                        </div>

                                        <div class="form-group">
                                            <label>Owner Email</label>
                                            <input type="email" name="owner_email" class="form-control" value="{{ old('owner_email') }}" placeholder="e.g., owner@example.com">
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
                                                    <option value="{{ $countryId }}" {{ old('country_id') == $countryId ? 'selected' : '' }}>{{ $countryName }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>State</label>
                                            <select name="state_id" id="state_id" class="form-control select2">
                                                <option value="">{{__('admin.Select a State')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>City</label>
                                            <select name="city_id" id="city_id" class="form-control select2">
                                                <option value="">{{__('admin.Select a City')}}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h5 class="mb-3">GPS Coordinates</h5>

                                        <div class="form-group">
                                            <label>Latitude</label>
                                            <input type="number" name="lat" class="form-control" step="0.0000001" value="{{ old('lat') }}" placeholder="e.g., 12.9716">
                                        </div>

                                        <div class="form-group">
                                            <label>Longitude</label>
                                            <input type="number" name="lng" class="form-control" step="0.0000001" value="{{ old('lng') }}" placeholder="e.g., 77.5946">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Shop Settings</h5>

                                        <div class="form-group">
                                            <label>Commission Percent (%)</label>
                                            <div class="input-group">
                                                <input type="number" name="commission_percent" class="form-control" min="0" max="100" step="0.01" value="{{ old('commission_percent') }}" placeholder="e.g., 10">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Discount Percent (%)</label>
                                            <div class="input-group">
                                                <input type="number" name="discount_percent" class="form-control" min="0" max="100" step="0.01" value="{{ old('discount_percent') }}" placeholder="e.g., 5">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Rating (0-5)</label>
                                            <input type="number" name="rating" class="form-control" min="0" max="5" step="0.1" value="{{ old('rating') }}" placeholder="e.g., 4.5">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h5 class="mb-3">Store Image</h5>

                                        <div class="form-group">
                                            <label>Store Image</label>
                                            <input type="file" name="store_image" class="form-control-file" accept="image/*">
                                            <small class="text-muted">Recommended size: 400x400 pixels. Accepted formats: JPG, PNG, GIF</small>
                                        </div>

                                        <div class="form-group">
                                            <label>Additional Images</label>
                                            <input type="file" name="images[]" class="form-control-file" accept="image/*" multiple>
                                            <small class="text-muted">You can select multiple images. Accepted formats: JPG, PNG, GIF</small>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Bank Account Details</h5>

                                        <div class="form-group">
                                            <label>Bank Name</label>
                                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}" placeholder="e.g., State Bank of India">
                                        </div>

                                        <div class="form-group">
                                            <label>Account Number</label>
                                            <input type="text" name="account_number" class="form-control" value="{{ old('account_number') }}" placeholder="e.g., 1234567890123456" maxlength="50">
                                        </div>

                                        <div class="form-group">
                                            <label>IFSC Code</label>
                                            <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code') }}" placeholder="e.g., SBIN0001234" maxlength="20" style="text-transform: uppercase;">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h5 class="mb-3">&nbsp;</h5>

                                        <div class="form-group">
                                            <label>Beneficiary Name</label>
                                            <input type="text" name="beneficiary_name" class="form-control" value="{{ old('beneficiary_name') }}" placeholder="Account holder's name">
                                        </div>

                                        <div class="form-group">
                                            <label>UPI ID</label>
                                            <input type="text" name="upi_id" class="form-control" value="{{ old('upi_id') }}" placeholder="e.g., example@upi">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-plus"></i> Create Application
                                        </button>
                                        <a href="{{ route('admin.seller-applications.index') }}" class="btn btn-secondary btn-lg">
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
