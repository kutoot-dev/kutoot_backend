@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Currency')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Currency')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Currency')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.currency.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{__('admin.SN')}}</th>
                                    <th>{{__('admin.Currency Name')}}</th>
                                    <th>{{__('admin.Country Code')}}</th>
                                    <th>{{__('admin.Currency Code')}}</th>
                                    <th>{{__('admin.Currency Icon')}}</th>
                                    <th>{{__('admin.Currency Rate')}}</th>
                                    <th>{{__('admin.Default')}}</th>
                                    <th>{{__('admin.Status')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($currencies as $index => $currency)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $currency->currency_name }}</td>
                                        <td>{{ $currency->country_code }}</td>
                                        <td>{{ $currency->currency_code }}</td>
                                        <td>{{ $currency->currency_icon }}</td>
                                        <td>{{ $currency->currency_rate }}</td>

                                        <td>
                                            @if ($currency->is_default == 'Yes')
                                                <span class="badge badge-success">{{__('admin.Yes')}}</span>
                                            @else
                                            <span class="badge badge-danger">{{__('admin.No')}}</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($currency->status == 1)
                                                <span class="badge badge-success">{{__('admin.Active')}}</span>
                                            @else
                                            <span class="badge badge-danger">{{__('admin.In-active')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.currency.edit',$currency->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>

                                            @if ($currency->id != 1)
                                                <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $currency->id }})" disabled><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                  @endforeach
                            </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

<script>
    "use strict";
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/currency/") }}'+"/"+id)
    }
    function changeProductCategoryStatus(id){
        var isDemo = "{{ env('APP_MODE') }}"
        if(isDemo == 'DEMO'){
            toastr.error('This Is Demo Version. You Can Not Change Anything');
            return;
        }
        $.ajax({
            type:"put",
            data: { _token : '{{ csrf_token() }}' },
            url:"{{url('/admin/category-status/')}}"+"/"+id,
            success:function(response){
                toastr.success(response)
            },
            error:function(err){


            }
        })
    }
</script>
@endsection
