@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Home Page One Visibility')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Home Page One Visibility')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Home Page One Visibility')}}</div>
            </div>
          </div>

        <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-3">
                                    <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">


                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link active" id="slider-tab" data-toggle="tab" href="#sliderTab" role="tab" aria-controls="sliderTab" aria-selected="true">{{__('admin.Slider')}}</a>
                                        </li>

                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link" id="brand-tab" data-toggle="tab" href="#brandTab" role="tab" aria-controls="brandTab" aria-selected="true">{{__('admin.Brand')}}</a>
                                        </li>

                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link" id="campaign-tab" data-toggle="tab" href="#campaignTab" role="tab" aria-controls="campaignTab" aria-selected="true">{{__('admin.Campaign')}}</a>
                                        </li>

                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link" id="one-col-banner-tab" data-toggle="tab" href="#oneColBannerTab" role="tab" aria-controls="oneColBannerTab" aria-selected="true">{{__('admin.Popular Category')}}</a>
                                        </li>

                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link" id="first-two-col-banner-tab" data-toggle="tab" href="#firstTwoColBannerTab" role="tab" aria-controls="firstTwoColBannerTab" aria-selected="true">{{__('admin.First Two column banner')}}</a>
                                        </li>

                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link" id="flash-deal-tab" data-toggle="tab" href="#flashDealTab" role="tab" aria-controls="flashDealTab" aria-selected="true">{{__('admin.Flash Deal')}}</a>
                                        </li>

                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link" id="highlight-tab" data-toggle="tab" href="#highlightTab" role="tab" aria-controls="highlightTab" aria-selected="true">{{__('admin.Product Highlight')}}</a>
                                        </li>

                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link" id="second-two-col-banner-tab" data-toggle="tab" href="#secondTwoColBannerTab" role="tab" aria-controls="secondTwoColBannerTab" aria-selected="true">{{__('admin.Second Two Column Banner')}}</a>
                                        </li>

                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link" id="three-col-category-tab" data-toggle="tab" href="#threeColCategoryTab" role="tab" aria-controls="threeColCategoryTab" aria-selected="true">{{__('admin.Three Column Category')}}</a>
                                        </li>

                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link" id="third-two-col-banner-tab" data-toggle="tab" href="#thirdTwoColBannerTab" role="tab" aria-controls="thirdTwoColBannerTab" aria-selected="true">{{__('admin.Third Two Column Banner')}}</a>
                                        </li>

                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link" id="service-tab" data-toggle="tab" href="#serviceTab" role="tab" aria-controls="serviceTab" aria-selected="true">{{__('admin.Service')}}</a>
                                        </li>

                                        <li class="nav-item border rounded mb-1">
                                            <a class="nav-link" id="blog-tab" data-toggle="tab" href="#blogTab" role="tab" aria-controls="blogTab" aria-selected="true">{{__('admin.Blog')}}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-12 col-sm-12 col-md-9">
                                    <div class="border rounded">
                                        <div class="tab-content no-padding" id="settingsContent">

                                            <div class="tab-pane fade show active" id="sliderTab" role="tabpanel" aria-labelledby="slider-tab">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        @php
                                                            $section = $sections->where('id','1')->first();
                                                        @endphp
                                                        <form action="{{ route('admin.update-homepage-one-visibility', $section->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">{{__('admin.Status')}}</label>
                                                                        <div>
                                                                            @if ($section->status == 1)
                                                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                            @else
                                                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">{{__('admin.Quantity')}}</label>
                                                                        <input type="number" name="qty" class="form-control" value="{{ $section->qty }}">
                                                                    </div>
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

                                            <div class="tab-pane fade" id="brandTab" role="tabpanel" aria-labelledby="brand-tab">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        @php
                                                            $section = $sections->where('id','2')->first();
                                                        @endphp
                                                        <form action="{{ route('admin.update-homepage-one-visibility', $section->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        @php
                                                                            $status = 1
                                                                        @endphp
                                                                        <label for="">{{__('admin.Status')}}</label>
                                                                        <div>
                                                                            @if ($section->status == 1)
                                                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                            @else
                                                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">{{__('admin.Quantity')}}</label>
                                                                        <input type="number" name="qty" class="form-control"  value="{{ $section->qty }}">
                                                                    </div>
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

                                            <div class="tab-pane fade" id="campaignTab" role="tabpanel" aria-labelledby="campaign-tab">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        @php
                                                            $section = $sections->where('id','3')->first();
                                                        @endphp
                                                        <form action="{{ route('admin.update-homepage-one-visibility', $section->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        @php
                                                                            $status = 1
                                                                        @endphp
                                                                        <label for="">{{__('admin.Status')}}</label>
                                                                        <div>
                                                                            @if ($section->status == 1)
                                                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                            @else
                                                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">{{__('admin.Quantity')}}</label>
                                                                        <input type="number" name="qty" class="form-control"  value="{{ $section->qty }}">
                                                                    </div>
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

                                            <div class="tab-pane fade" id="oneColBannerTab" role="tabpanel" aria-labelledby="one-col-banner-tab">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        @php
                                                            $section = $sections->where('id','4')->first();
                                                        @endphp
                                                        <form action="{{ route('admin.update-homepage-one-visibility', $section->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        @php
                                                                            $status = 1
                                                                        @endphp
                                                                        <label for="">{{__('admin.Status')}}</label>
                                                                        <div>
                                                                            @if ($section->status == 1)
                                                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                            @else
                                                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @endif
                                                                        </div>
                                                                    </div>
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

                                            <div class="tab-pane fade" id="firstTwoColBannerTab" role="tabpanel" aria-labelledby="first-two-col-banner-tab">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        @php
                                                            $section = $sections->where('id','5')->first();
                                                        @endphp
                                                        <form action="{{ route('admin.update-homepage-one-visibility', $section->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        @php
                                                                            $status = 1
                                                                        @endphp
                                                                        <label for="">{{__('admin.Status')}}</label>
                                                                        <div>
                                                                            @if ($section->status == 1)
                                                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                            @else
                                                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @endif
                                                                        </div>
                                                                    </div>
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

                                            <div class="tab-pane fade" id="flashDealTab" role="tabpanel" aria-labelledby="flash-deal-tab">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        @php
                                                            $section = $sections->where('id','6')->first();
                                                        @endphp
                                                        <form action="{{ route('admin.update-homepage-one-visibility', $section->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        @php
                                                                            $status = 1
                                                                        @endphp
                                                                        <label for="">{{__('admin.Status')}}</label>
                                                                        <div>
                                                                            @if ($section->status == 1)
                                                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                            @else
                                                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">{{__('admin.Quantity')}}</label>
                                                                        <input type="number" name="qty" class="form-control"  value="{{ $section->qty }}">
                                                                    </div>
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

                                            <div class="tab-pane fade" id="highlightTab" role="tabpanel" aria-labelledby="highlight-tab">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        @php
                                                            $section = $sections->where('id','7')->first();
                                                        @endphp
                                                        <form action="{{ route('admin.update-homepage-one-visibility', $section->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        @php
                                                                            $status = 1
                                                                        @endphp
                                                                        <label for="">{{__('admin.Status')}}</label>
                                                                        <div>
                                                                            @if ($section->status == 1)
                                                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                            @else
                                                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">{{__('admin.Quantity')}}</label>
                                                                        <input type="number" name="qty" class="form-control"  value="{{ $section->qty }}">
                                                                    </div>
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

                                            <div class="tab-pane fade" id="secondTwoColBannerTab" role="tabpanel" aria-labelledby="second-two-col-banner-tab">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        @php
                                                            $section = $sections->where('id','8')->first();
                                                        @endphp
                                                        <form action="{{ route('admin.update-homepage-one-visibility', $section->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        @php
                                                                            $status = 1
                                                                        @endphp
                                                                        <label for="">{{__('admin.Status')}}</label>
                                                                        <div>
                                                                            @if ($section->status == 1)
                                                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                            @else
                                                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @endif
                                                                        </div>
                                                                    </div>
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

                                            <div class="tab-pane fade" id="threeColCategoryTab" role="tabpanel" aria-labelledby="three-column-category-tab">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        @php
                                                            $section = $sections->where('id','9')->first();
                                                        @endphp
                                                        <form action="{{ route('admin.update-homepage-one-visibility', $section->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        @php
                                                                            $status = 1
                                                                        @endphp
                                                                        <label for="">{{__('admin.Status')}}</label>
                                                                        <div>
                                                                            @if ($section->status == 1)
                                                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                            @else
                                                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">{{__('admin.Quantity')}}</label>
                                                                        <input type="number" name="qty" class="form-control"  value="{{ $section->qty }}">
                                                                    </div>
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

                                            <div class="tab-pane fade" id="thirdTwoColBannerTab" role="tabpanel" aria-labelledby="third-two-col-banner-tab">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        @php
                                                            $section = $sections->where('id','10')->first();
                                                        @endphp
                                                        <form action="{{ route('admin.update-homepage-one-visibility', $section->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        @php
                                                                            $status = 1
                                                                        @endphp
                                                                        <label for="">{{__('admin.Status')}}</label>
                                                                        <div>
                                                                            @if ($section->status == 1)
                                                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                            @else
                                                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @endif
                                                                        </div>
                                                                    </div>
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

                                            <div class="tab-pane fade" id="serviceTab" role="tabpanel" aria-labelledby="service-tab">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        @php
                                                            $section = $sections->where('id','11')->first();
                                                        @endphp
                                                        <form action="{{ route('admin.update-homepage-one-visibility', $section->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        @php
                                                                            $status = 1
                                                                        @endphp
                                                                        <label for="">{{__('admin.Status')}}</label>
                                                                        <div>
                                                                            @if ($section->status == 1)
                                                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                            @else
                                                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">{{__('admin.Quantity')}}</label>
                                                                        <input type="number" name="qty" class="form-control"  value="{{ $section->qty }}">
                                                                    </div>
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

                                            <div class="tab-pane fade" id="blogTab" role="tabpanel" aria-labelledby="blog-tab">
                                                <div class="card m-0">
                                                    <div class="card-body">
                                                        @php
                                                            $section = $sections->where('id','12')->first();
                                                        @endphp
                                                        <form action="{{ route('admin.update-homepage-one-visibility', $section->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        @php
                                                                            $status = 1
                                                                        @endphp
                                                                        <label for="">{{__('admin.Status')}}</label>
                                                                        <div>
                                                                            @if ($section->status == 1)
                                                                            <input id="status_toggle" type="checkbox" checked data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                            @else
                                                                            <input id="status_toggle" type="checkbox" data-toggle="toggle" data-on="{{__('admin.Enable')}}" data-off="{{__('admin.Disable')}}" data-onstyle="success" data-offstyle="danger" name="status">
                                                                        @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="">{{__('admin.Quantity')}}</label>
                                                                        <input type="number" name="qty" class="form-control"  value="{{ $section->qty }}">
                                                                    </div>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        </section>
      </div>

@endsection
