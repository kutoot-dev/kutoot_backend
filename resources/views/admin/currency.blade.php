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
            <div class="row mb-3">
                <div class="col-md-6">
                    {{-- World package currencies are read-only --}}
                </div>
                <div class="col-md-6">
                    <form action="" method="GET" class="float-right">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{__('admin.Search')}}..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{__('admin.SN')}}</th>
                                    <th>{{__('admin.Currency Name')}}</th>
                                    <th>{{__('admin.Country')}}</th>
                                    <th>{{__('admin.Currency Code')}}</th>
                                    <th>{{__('admin.Symbol')}}</th>
                                    <th>{{__('admin.Native Symbol')}}</th>
                                    <th>{{__('admin.Precision')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($currencies as $index => $currency)
                                    <tr>
                                        <td>{{ $currencies->firstItem() + $index }}</td>
                                        <td>{{ $currency->name }}</td>
                                        <td>{{ $currency->country->name ?? 'N/A' }}</td>
                                        <td>{{ $currency->code }}</td>
                                        <td>{{ $currency->symbol }}</td>
                                        <td>{{ $currency->symbol_native }}</td>
                                        <td>{{ $currency->precision }}</td>
                                    </tr>
                                  @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $currencies->links('pagination::bootstrap-4') }}
                        </div>
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
