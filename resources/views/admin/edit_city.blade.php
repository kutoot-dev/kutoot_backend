@extends('admin.master_layout')

@section('title')
    <title>{{ __('admin.City') }}</title>
@endsection

@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{ __('admin.Edit City') }}</h1>

            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active">
                    <a href="{{ route('admin.dashboard') }}">{{ __('admin.Dashboard') }}</a>
                </div>
                <div class="breadcrumb-item active">
                    <a href="{{ route('admin.city.index') }}">{{ __('admin.City') }}</a>
                </div>
                <div class="breadcrumb-item">{{ __('admin.Edit City') }}</div>
            </div>
        </div>

        <div class="section-body">
            <a href="{{ route('admin.city.index') }}" class="btn btn-primary mb-3">
                <i class="fas fa-list"></i> {{ __('admin.City') }}
            </a>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            {{-- FORM --}}
                            <form action="{{ route('admin.city.update', $city->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">

                                    {{-- COUNTRY --}}
                                    <div class="form-group col-12">
                                        <label>{{ __('admin.Country') }} <span class="text-danger">*</span></label>
                                        <select name="country" id="country_id" class="form-control select2">
                                            <option value="">{{ __('admin.Select Country') }}</option>

                                            @forelse ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ $city->country_id == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @empty
                                                <option disabled>{{ __('admin.No Country Found') }}</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    {{-- STATE --}}
                                    <div class="form-group col-12">
                                        <label>{{ __('admin.State') }} <span class="text-danger">*</span></label>
                                        <select name="state" id="state_id" class="form-control select2">
                                            <option value="">{{ __('admin.Select State') }}</option>

                                            @forelse ($states as $state)
                                                <option value="{{ $state->id }}"
                                                    {{ $city->state_id == $state->id ? 'selected' : '' }}>
                                                    {{ $state->name }}
                                                </option>
                                            @empty
                                                <option disabled>{{ __('admin.No State Found') }}</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    {{-- CITY NAME --}}
                                    <div class="form-group col-12">
                                        <label>{{ __('admin.City Name') }} <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="name"
                                               class="form-control"
                                               value="{{ $city->name ?? '' }}">
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary">
                                            {{ __('admin.Save') }}
                                        </button>
                                    </div>
                                </div>

                            </form>
                            {{-- END FORM --}}

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

{{-- AJAX SCRIPT --}}
<script>
(function ($) {
    "use strict";

    $(document).ready(function () {

        $("#country_id").on("change", function () {
            let countryId = $(this).val();

            if (countryId) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('/admin/state-by-country') }}/" + countryId,
                    success: function (response) {
                        $("#state_id").html(response.states);
                    },
                    error: function (err) {
                        console.error(err);
                    }
                });
            } else {
                $("#state_id").html(
                    "<option value=''>{{ __('admin.Select State') }}</option>"
                );
            }
        });

    });

})(jQuery);
</script>
@endsection
