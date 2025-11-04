@extends('seller.master_layout')
@section('title')
<title>{{__('admin.Products Inventory')}}</title>
@endsection
@section('seller-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.Products Inventory')}}</h1>

          </div>

          <div class="section-body">
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th width="5%">{{__('admin.SN')}}</th>
                                    <th width="30%">{{__('admin.Name')}}</th>
                                    <th width="15%">{{__('admin.SKU')}}</th>
                                    <th width="15%">{{__('admin.Stock')}}</th>
                                    <th width="15%">{{__('admin.Sold')}}</th>
                                    <th width="15%">{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $index => $product)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>
                                            <a href="{{ route('seller.product.edit', $product->id) }}">{{ $product->short_name }}</a>
                                        </td>
                                        <td>{{ $product->sku }}</td>
                                        <td>{{ $product->qty }}</td>
                                        <td>{{ $product->total_sold }}</td>
                                        <td>
                                            <a class="btn btn-success btn-sm" href="{{ route('seller.stock-history', $product->id) }}"><i class="fa fa-eye" aria-hidden="true"></i> </a>
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

@endsection
