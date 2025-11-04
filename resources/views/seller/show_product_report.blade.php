@extends('seller.master_layout')
@section('title')
<title>{{__('admin.Product report')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Product report')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('seller.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item active"><a href="{{ route('seller.product.index') }}">{{__('admin.Product')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.Product report')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('seller.product-report') }}" class="btn btn-primary"><i class="fas fa-list"></i> {{__('admin.Product report')}}</a>
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
                               <td><a href="{{ route('seller.product.edit', $report->product->id) }}">{{ $report->product->name }}</a></td>
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
                               <td><a href="">{{ $totalReport }}</a></td>
                           </tr>

                           <tr>
                               <td>{{__('admin.Subject')}}</td>
                               <td>{{ $report->subject }}</td>
                           </tr>
                           <tr>
                               <td>{{__('admin.Description')}}</td>
                               <td>{{ $report->description }}</td>
                           </tr>

                        </table>
                      </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>
@endsection
