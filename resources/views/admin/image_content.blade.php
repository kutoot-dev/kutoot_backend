@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Image Content')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Image Content')}}</h1>
          </div>

        <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.update-image-content') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label>{{__('admin.Empty Cart Image')}}</label>
                                        <div>
                                            <img src="{{ asset($image_content->empty_cart) }}" alt="" width="200px">
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        <label>{{__('admin.New Image')}}</label>
                                        <input type="file" name="empty_cart" class="form-control-file">
                                    </div>

                                    <div class="form-group col-12">
                                        <label>{{__('admin.Empty Wishlist Image')}}</label>
                                        <div>
                                            <img src="{{ asset($image_content->empty_wishlist) }}" alt="" width="200px">
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        <label>{{__('admin.New Image')}}</label>
                                        <input type="file" name="empty_wishlist" class="form-control-file">
                                    </div>

                                    <div class="form-group col-12">
                                        <label>{{__('admin.Change Password Image')}}</label>
                                        <div>
                                            <img src="{{ asset($image_content->change_password_image) }}" alt="" width="200px">
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        <label>{{__('admin.New Image')}}</label>
                                        <input type="file" name="change_password_image" class="form-control-file">
                                    </div>

                                    <div class="form-group col-12">
                                        <label>{{__('admin.Become seller Avatar')}}</label>
                                        <div>
                                            <img src="{{ asset($image_content->become_seller_avatar) }}" alt="" width="200px">
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        <label>{{__('admin.New Image')}}</label>
                                        <input type="file" name="become_seller_avatar" class="form-control-file">
                                    </div>

                                    <div class="form-group col-12">
                                        <label>{{__('admin.Become Seller Banner')}}</label>
                                        <div>
                                            <img src="{{ asset($image_content->become_seller_banner) }}" alt="" width="200px">
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        <label>{{__('admin.New Image')}}</label>
                                        <input type="file" name="become_seller_banner" class="form-control-file">
                                    </div>

                                    <div class="form-group col-12">
                                        <label>{{__('admin.Login Page')}}</label>
                                        <div>
                                            <img src="{{ asset($image_content->login_image) }}" alt="" width="200px">
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        <label>{{__('admin.New Image')}}</label>
                                        <input type="file" name="login_image" class="form-control-file">
                                    </div>

                                    <div class="form-group col-12">
                                        <label>{{__('admin.Error Page')}}</label>
                                        <div>
                                            <img src="{{ asset($image_content->error_page) }}" alt="" width="200px">
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        <label>{{__('admin.New Image')}}</label>
                                        <input type="file" name="error_page" class="form-control-file">
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


        </section>
      </div>

@endsection
