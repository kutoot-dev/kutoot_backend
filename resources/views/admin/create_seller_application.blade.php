@extends('admin.master_layout')
@section('title')
<title>Create Store Application</title>
@endsection

{{-- Leaflet CSS for map --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
{{-- Leaflet JS for map --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
                                            <input type="number" name="lat" id="lat" class="form-control" step="0.0000001" value="{{ old('lat') }}" placeholder="e.g., 12.9716">
                                        </div>

                                        <div class="form-group">
                                            <label>Longitude</label>
                                            <input type="number" name="lng" id="lng" class="form-control" step="0.0000001" value="{{ old('lng') }}" placeholder="e.g., 77.5946">
                                        </div>

                                        <div class="form-group" id="googleMapsLinkContainer" style="display: none;">
                                            <a href="#" id="googleMapsLink" target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-external-link-alt"></i> View on Google Maps
                                            </a>
                                        </div>

                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-info" onclick="getCurrentLocation()">
                                                    <i class="fas fa-crosshairs"></i> Current Location
                                                </button>
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mapModal">
                                                    <i class="fas fa-map-marker-alt"></i> Select on Map
                                                </button>
                                            </div>
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

{{-- Map Modal for Location Selection --}}
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Select Location on Map</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="map" style="height: 400px; width: 100%;"></div>
                <div class="mt-2 text-muted">
                    <small>Click on the map to select a location or use the "Current Location" button</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmLocation()">Confirm Location</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";

        let map = null;
        let marker = null;

        // Initialize map when modal is shown
        $('#mapModal').on('shown.bs.modal', function () {
            setTimeout(function() {
                // Try to get current location when map modal opens
                if (navigator.geolocation && !$('#lat').val() && !$('#lng').val()) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            $('#lat').val(lat.toFixed(7));
                            $('#lng').val(lng.toFixed(7));
                            // Update Google Maps link
                            updateGoogleMapsLink(lat.toFixed(7), lng.toFixed(7));
                            initMap();
                        },
                        function() {
                            // Fallback to default if geolocation fails
                            initMap();
                        },
                        { enableHighAccuracy: true, timeout: 5000 }
                    );
                } else {
                    if (map) {
                        map.invalidateSize();
                    } else {
                        initMap();
                    }
                }
            }, 300); // Increased timeout for modal animation to complete
        });

        function initMap() {
            // Get initial coordinates from inputs or default to center
            const lat = parseFloat($('#lat').val()) || 20.5937; // Default to India center
            const lng = parseFloat($('#lng').val()) || 78.9629;

            // Initialize Leaflet map
            map = L.map('map').setView([lat, lng], 13);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Add marker if coordinates exist
            if ($('#lat').val() && $('#lng').val()) {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            }

            // Add click handler to place marker
            map.on('click', function(e) {
                if (marker) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = L.marker(e.latlng, { draggable: true }).addTo(map);
                }
                updateCoordinates(e.latlng);
            });

            // Update coordinates when marker is dragged
            if (marker) {
                marker.on('dragend', function(e) {
                    updateCoordinates(e.target.getLatLng());
                });
            }
        }

        function updateCoordinates(latlng) {
            $('#lat').val(latlng.lat.toFixed(7));
            $('#lng').val(latlng.lng.toFixed(7));
            // Update Google Maps link
            updateGoogleMapsLink(latlng.lat.toFixed(7), latlng.lng.toFixed(7));
        }

        // Update Google Maps link dynamically
        function updateGoogleMapsLink(lat, lng) {
            const $link = $('#googleMapsLink');
            const $container = $('#googleMapsLinkContainer');
            if (lat && lng) {
                $link.attr('href', 'https://www.google.com/maps?q=' + lat + ',' + lng);
                $container.show();
            } else {
                $container.hide();
            }
        }

        // Listen for manual input changes on lat/lng fields
        $('#lat, #lng').on('input change', function() {
            const lat = $('#lat').val();
            const lng = $('#lng').val();
            updateGoogleMapsLink(lat, lng);
        });

        // Get current location using Geolocation API
        window.getCurrentLocation = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        $('#lat').val(lat.toFixed(7));
                        $('#lng').val(lng.toFixed(7));

                        // Update Google Maps link
                        updateGoogleMapsLink(lat.toFixed(7), lng.toFixed(7));

                        // Update map if it's initialized
                        if (map) {
                            map.setView([lat, lng], 15);
                            if (marker) {
                                marker.setLatLng([lat, lng]);
                            } else {
                                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                                marker.on('dragend', function(e) {
                                    updateCoordinates(e.target.getLatLng());
                                });
                            }
                        }

                        // Show modal with current location if map is not visible
                        if (!$('#mapModal').hasClass('show')) {
                            $('#mapModal').modal('show');
                        }

                        alert('Location found! Latitude: ' + lat.toFixed(5) + ', Longitude: ' + lng.toFixed(5));
                    },
                    function(error) {
                        let errorMessage = 'Unable to get your location.';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = 'Location access was denied. Please enable location permissions.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = 'Location information is unavailable.';
                                break;
                            case error.TIMEOUT:
                                errorMessage = 'Location request timed out.';
                                break;
                        }
                        alert(errorMessage);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        };

        // Confirm location from modal
        window.confirmLocation = function() {
            $('#mapModal').modal('hide');
        };

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
