@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Product Report')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Product Report')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('admin.product.index') }}">{{__('admin.Product')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Product Report')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.product-report') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Product Report')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped table-bordered">
                           <tr>
                               <td>{{__('admin.User Name')}}</td>
                               <td>{{ $report->user->name }}</td>
                           </tr>
                           <tr>
                               <td>{{__('admin.User Email')}}</td>
                               <td>{{ $report->user->email }}</td>
                           </tr>
                           <tr>
                               <td>{{__('admin.Product')}}</td>
                               <td><a href="{{ route('admin.product.edit', $report->product->id) }}">{{ $report->product->name }}</a></td>
                           </tr>
                           <tr>
                               <td>{{__('admin.Product Status')}}</td>
                               <td>

                                    @if ($report->product->approve_by_admin == 1)
                                    @if($report->product->status == 1)
                                        <span class="badge badge-success">{{__('admin.Active')}}</span>
                                    @else
                                        <span class="badge badge-danger">{{__('admin.Inactive')}}</span>
                                    @endif
                                    @else
                                        <span class="badge badge-danger">{{__('admin.Pending')}}</span>
                                    @endif
                               </td>
                           </tr>

                           <tr>
                                <td>{{__('admin.Total reports under this product')}}</td>
                                <td>{{ $totalReport }}</td>
                            </tr>

                           @if ($report->seller_id != 0)
                                <tr>
                                    <td>{{__('admin.Seller')}}</td>
                                    <td><a href="{{ route('admin.seller-show',$report->seller_id) }}">{{ $report->seller->user->name }}</a></td>
                                </tr>
                           @endif

                           <tr>
                               <td>{{__('admin.Subject')}}</td>
                               <td>{{ $report->subject }}</td>
                           </tr>
                           <tr>
                               <td>{{__('admin.Description')}}</td>
                               <td>{{ $report->description }}</td>
                           </tr>

                        </table>

                        <form action="{{ route('admin.de-active-product', $report->id) }}" method="POST" id="deactive-product">
                            @csrf
                            @method('PUT')
                        </form>

                        @if ($report->product->status == 1)
                        <a href="{{ route('admin.de-active-product',$report->id) }}" class="btn btn-primary" onclick="event.preventDefault();
                        document.getElementById('deactive-product').submit();">{{__('admin.Deactive product')}}</a>
                        @endif
                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger" onclick="deleteData({{ $report->id }})">{{__('admin.Delete Report')}}</a>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

      <script>
        function deleteData(id){
            $("#deleteForm").attr("action",'{{ url("admin/delete-product-report/") }}'+"/"+id)
        }
    </script>
@endsection
