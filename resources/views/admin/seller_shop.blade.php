
@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Shop Details')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Shop Details')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.seller-show',$seller->id) }}">{{__('admin.Seller Profile')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Shop Details')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.seller-show',$seller->id) }}" class="btn btn-primary"><i class="fas fa-user"></i> {{ $user->name }}</a>
            <div class="row ">
              <div class="col-12">
                <div class="card profile-widget">
                  <div class="profile-widget-description">
                    <form action="{{ route('admin.update-seller-shop',$seller->id) }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('PUT')
                        <div class="row">
                            <div class="form-group col-12">
                                <label>{{__('admin.Current Logo')}}</label>
                                <div>
                                    <img src="{{ asset($seller->logo) }}" width="100px" alt="">
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <label>{{__('admin.New Logo')}}</label>
                                <input type="file" class="form-control-file" name="logo">
                            </div>

                            <div class="form-group col-12">
                                <label>{{__('admin.Current Banner Image')}}</label>
                                <div>
                                    <img src="{{ asset($seller->banner_image) }}" width="300px" alt="">
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <label>{{__('admin.New Banner Image')}}</label>
                                <input type="file" class="form-control-file" name="banner_image">
                            </div>




                            <div class="form-group col-12">
                                <label>{{__('admin.Shop Name')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{ $seller->shop_name }}" name="shop_name" readonly>
                            </div>

                            <div class="form-group col-12">
                                <label>{{__('admin.Email')}} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" value="{{ $seller->email }}" name="email" readonly>
                            </div>

                            <div class="form-group col-12">
                                <label>{{__('admin.Phone')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{ $seller->phone }}" name="phone">
                            </div>

                            <div class="form-group col-12">
                                <label>{{__('admin.Opens at')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control clockpicker" value="{{ $seller->open_at }}" data-align="top" data-autoclose="true" name="opens_at">
                            </div>

                            <div class="form-group col-12">
                                <label>{{__('admin.Closed at')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control clockpicker" value="{{ $seller->closed_at }}" data-align="top" data-autoclose="true" name="closed_at">

                            </div>

                            <div class="form-group col-12">
                                <label>{{__('admin.Address')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{ $seller->address }}" name="address">
                            </div>

                            <div class="form-group col-12">
                                <label>{{__('admin.Greeting Message for Chatbox')}} <span class="text-danger">*</span></label>
                                <textarea name="greeting_msg" id="greeting_msg" class="form-control text-area-5" cols="30" rows="10">{{ $seller->greeting_msg }}</textarea>
                            </div>

                            <div class="col-12" id="socialBox">
                                @if ($seller->socialLinks->count() !=0 )
                                    @foreach ($seller->socialLinks as $socialLink)
                                        <div class="row" id="existingSocialLink-{{ $socialLink->id }}">
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <label for="">{{__('admin.Social Icon')}}</label>
                                                    <input type="text" class="form-control custom-icon-picker" value="{{ $socialLink->icon }}" name="icons[]">
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <label for="">{{__('admin.Social Link')}}</label>
                                                    <input type="text" class="form-control" value="{{ $socialLink->link }}" name="links[]">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <button type="button" class="btn btn-danger add_new_social_btn" id="removeBox-{{ $socialLink->id }}" onclick="deleteSocialLink('{{ $socialLink->id }}')"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="row">
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label for="">{{__('admin.Social Icon')}}</label>
                                            <input type="text" class="form-control custom-icon-picker" name="icons[]">
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label for="">{{__('admin.Social Link')}}</label>
                                            <input type="text" class="form-control" name="links[]">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <button type="button" class="btn btn-success add_new_social_btn" id="addNewIcon"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                    </div>
                                </div>

                            </div>

                            <div id="socialIconHiddenBox" class="d-none">
                                <div class="row remove-box">
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label for="">{{__('admin.Social Icon')}}</label>
                                            <input type="text" class="form-control custom-icon-picker" name="icons[]">
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="form-group">
                                            <label for="">{{__('admin.Social Link')}}</label>
                                            <input type="text" class="form-control" name="links[]">
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <button type="button" class="btn btn-danger add_new_social_btn removeSocialBox"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            </div>



                            <div class="form-group col-12">
                                <label>{{__('admin.Seo Title')}}</label>
                                <input type="text" class="form-control" value="{{ $seller->seo_title }}" name="seo_title">
                            </div>



                            <div class="form-group col-12">
                                <label>{{__('admin.Seo Description')}}</label>
                                <textarea name="seo_description" class="form-control text-area-5" id="" cols="30" rows="10">{{ $seller->seo_description }}</textarea>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-primary">{{__('admin.Save Changes')}}</button>
                            </div>
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
    (function($) {
        "use strict";
        $(document).ready(function () {

            $("#addNewIcon").on("click", function(){
                var html = $("#socialIconHiddenBox").html();
                $("#socialBox").append(html);
                $('.custom-icon-picker').iconpicker({
                templates: {
                    popover: '<div class="iconpicker-popover popover"><div class="arrow"></div>' +
                        '<div class="popover-title"></div><div class="popover-content"></div></div>',
                    footer: '<div class="popover-footer"></div>',
                    buttons: '<button class="iconpicker-btn iconpicker-btn-cancel btn btn-default btn-sm">Cancel</button>' +
                        ' <button class="iconpicker-btn iconpicker-btn-accept btn btn-primary btn-sm">Accept</button>',
                    search: '<input type="search" class="form-control iconpicker-search" placeholder="Type to filter" />',
                    iconpicker: '<div class="iconpicker"><div class="iconpicker-items"></div></div>',
                    iconpickerItem: '<a role="button" href="javascript:;" class="iconpicker-item"><i></i></a>'
                }
            })

            })

            $(document).on('click', '.removeSocialBox', function () {
                $(this).closest('.remove-box').remove();
            });


        });
    })(jQuery);

    function deleteSocialLink(id){
        var isDemo = "{{ env('APP_VERSION') }}"
        if(isDemo == 0){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url: "{{ url('admin/remove-seller-social-link/') }}"+"/"+ id,
            success: function (response) {
                if(response.success){
                    toastr.success(response.success)
                    $("#existingSocialLink-"+id).remove()
                }

            },
            error: function(err) {
                console.log(err);
            }
        });
    }
</script>
@endsection
