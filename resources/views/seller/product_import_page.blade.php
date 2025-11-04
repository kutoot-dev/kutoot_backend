@extends('seller.master_layout')
@section('title')
<title>{{__('admin.Product Bulk Import')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Product Bulk Import')}}</h1>

          </div>

          <div class="section-body">
            <a href="{{ route('seller.product.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Product List')}}</a>

            <a href="{{ route('seller.product-export') }}" class="btn btn-success"><i class="fas fa-file-export"></i> {{__('admin.Export Product List')}}</a>

            <a href="{{ route('seller.product-demo-export') }}" class="btn btn-primary"><i class="fas fa-file-export"></i> {{__('admin.Demo Export')}}</a>

            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('seller.product-import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('admin.Import File')}} <span class="text-danger">*</span></label>
                                    <input type="file" id="name" class="form-control-file"  name="import_file" required>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('admin.Upload')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                  </div>
                </div>
          </div>

          <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        @php
                            $required = 'This Field is required';
                            $not_required = 'Not required';
                            $required_and_unique = 'This Field is required and unique';
                        @endphp
                     
                        <tr>
                            <td>{{__('Name')}}</td>
                            <td>{{ $required }}</td>
                        </tr>

                        <tr>
                            <td>{{__('Short Name')}}</td>
                            <td>{{ $required }}</td>
                        </tr>

                        <tr>
                            <td>{{__('Slug')}}</td>
                            <td>{{ $required_and_unique }} , {{__('Slug and manufacture part no both are same')}}</td>
                        </tr>
                         
                        <tr>
                            <td>{{__('Thumbnail Image')}}</td>
                            <td>{{ $required }}</td>
                        </tr>
                     
                          <tr>
                            <td>{{__('Vendor Id')}}</td>
                            <td>{{ $required }}. Haven't any seller or vendor please set 0</td>
                        </tr>

                        <tr>
                            <td>{{__('Category Id')}}</td>
                            <td>{{ $required }}.Please set to 1 for root category</td>
                        </tr>

                        <tr>
                            <td>{{__('Sub category id')}}</td>
                            <td> Haven't any sub category please set 0</td>
                        </tr>

                        <tr>
                            <td>{{__('Child category id')}}</td>
                            <td>Haven't any child category please set 0</td>
                        </tr>

                        <tr>
                            <td>{{__('Brand id')}}</td>
                            <td>Haven't any child category please set 0</td>
                        </tr>

                         <tr>
                            <td>{{__('Quantity or qty')}}</td>
                            <td>{{ $required }}.{{__('You can put only numeric value')}}</td>
                        </tr>

                        <tr>
                            <td>{{__('Weight')}}</td>
                            <td>{{ $required }}.{{__('You can put only numeric value')}}</td>
                        </tr>
                           <tr>
                            <td>{{__('Short description')}}</td>
                            <td>{{ $required }}.</td>
                        </tr>

                        <tr>
                            <td>{{__('Long description')}}</td>
                            <td>{{ $required }}.</td>
                        </tr>
                        <tr>
                            <td>{{__('Video link')}}</td>
                            <td>{{ $not_required }}.{{__('You can put only youtube video link or put null')}}</td>
                        </tr>
                      
                        <tr>
                            <td>{{__('Sku')}}</td>
                            <td>{{ $required }} - AlphaNumeric</td>
                        </tr>

                          <tr>
                            <td>{{__('Seo title')}}</td>
                            <td>{{ $required }}.</td>
                        </tr>

                          <tr>
                            <td>{{__('Seo description')}}</td>
                            <td>{{ $required }}.</td>
                        </tr>

                        <tr>
                            <td>{{__('Price')}}</td>
                            <td>{{ $required }}.{{__('Allowed only numeric value')}}</td>
                        </tr>

                        <tr>
                            <td>{{__('Offer price')}}</td>
                            <td>{{ $not_required }}.{{__('You can put only numeric value')}}</td>
                        </tr>

                        <tr>
                            <td>{{__('Show Homepage')}}</td>
                            <td>{{ $required }}.Please set 0 or 1. 0 means = No and 1 means = Yes</td>
                        </tr>

                        <tr>
                            <td>{{__('Undefined')}}</td>
                            <td>{{ $required }}.Please set to 0</td>
                        </tr>

                          <tr>
                            <td>{{__('Featured Product')}}</td>
                            <td>{{ $required }}. Please set 0 or 1. 0 means = No and 1 means = Yes</td>
                        </tr>

                           <tr>
                            <td>{{__('New Arrival or new_product')}}</td>
                            <td>{{ $required }}. Please set 0 or 1. 0 means = No and 1 means = Yes</td>
                        </tr>

                     

                        <tr>
                            <td>{{__('Top Product')}}</td>
                            <td>{{ $required }}. Please set 0 or 1. 0 means = No and 1 means = Yes</td>
                        </tr>

                     

                        <tr>
                            <td>{{__('Best Product')}}</td>
                            <td>{{ $required }}. Please set 0 or 1. 0 means = No and 1 means = Yes</td>
                        </tr>

                      

                        <tr>
                            <td>{{__('status')}}</td>
                            <td>{{ $required }}. Yes = 1, No= 0</td>
                        </tr>

                        <tr>
                            <td>{{__('Is specification')}}</td>
                            <td>{{ $required }}. Yes = 1, No= 0</td>
                        </tr>

                        <tr>
                            <td>{{__('Approve by admin')}}</td>
                            <td>{{ $required }}. Yes = 1, No= 0</td>
                        </tr>

                    </table>
                </div>
            </div>
        </div>

        </section>
      </div>

@endsection
