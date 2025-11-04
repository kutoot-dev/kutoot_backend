@extends('admin.master_layout')
@section('title')
<title>{{__('Shipping Rule Bulk Upload')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Shipping Rule Bulk Upload')}}</h1>

          </div>

          <div class="section-body">
            <a href="{{ route('admin.shipping.index') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('Shipping Rules')}}</a>

            <a href="{{ route('admin.shipping-export') }}" class="btn btn-success"><i class="fas fa-file-export"></i> {{__('Export Rule List')}}</a>

            <a href="{{ route('admin.shipping-demo-export') }}" class="btn btn-primary"><i class="fas fa-file-export"></i> {{__('Demo Export')}}</a>

            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.shipping-import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12">
                                    <label>{{__('File')}} <span class="text-danger">*</span></label>
                                    <input type="file" id="name" class="form-control-file"  name="import_file" required>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary">{{__('Upload')}}</button>
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
                    <h3>{{__('Instruction')}}</h3>
                    <table class="table table-bordered table-striped">

                        <tr>
                            <td>{{__('City Id')}}</td>
                            <td>{{__('Required Field. Please make sure that you put in valid city id or delivery area id from city table. If you want to set shipping rule for all delivery area please set Zero(0) as a city id')}}</td>
                        </tr>

                        <tr>
                            <td>{{__('Shipping Rule')}}</td>
                            <td>{{__('Required Field. Any kind of text allowed in this field')}}</td>
                        </tr>

                        <tr>
                            <td>{{__('Type')}}</td>
                            <td>{{__('Required Field. There are only 3 type of value allowed. if you put in another value the system will crused. See the allowed value : ')}}
                                <br>
                                1. base_on_price
                                <br>
                                2. base_on_weight
                                <br>
                                3. base_on_qty
                            </td>
                        </tr>


                        <tr>
                            <td>{{__('Shipping Fee')}}</td>
                            <td>{{__('Required Field. Only numeric value allowed. If you provide free shipping please set Zero(0)')}}</td>
                        </tr>

                        <tr>
                            <td>{{__('Condition From')}}</td>
                            <td>{{__('Required Field. Only numeric value allowed')}}</td>
                        </tr>

                        <tr>
                            <td>{{__('Condition To')}}</td>
                            <td>{{__('Required Field. Only numeric value allowed. If you want to set unlimited condition please set (-1) here')}}</td>
                        </tr>

                    </table>
                </div>
            </div>
          </div>

        </section>
      </div>

@endsection
