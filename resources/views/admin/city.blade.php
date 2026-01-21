@extends('admin.master_layout')
@section('title')
<title>{{__('admin.City / Delivery Area')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('admin.City / Delivery Area')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.City / Delivery Area')}}</div>
            </div>
          </div>

          <div class="section-body">
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('admin.city.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.Add New')}}</a>
                </div>
                <div class="col-md-6">
                    <form action="" method="GET" class="float-right">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search City..." value="{{ request('search') }}">
                            <select name="country_id" class="form-control ml-2" id="country_id_filter">
                                <option value="">All Countries</option>
                                @foreach($countries ?? [] as $country)
                                    <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                            <select name="state_id" class="form-control ml-2" id="state_id_filter">
                                <option value="">All States</option>
                                @foreach($states ?? [] as $state)
                                    <option value="{{ $state->id }}" {{ request('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                <script>
                    // Dynamically load states when country changes
                    document.addEventListener('DOMContentLoaded', function() {
                        var countrySelect = document.getElementById('country_id_filter');
                        var stateSelect = document.getElementById('state_id_filter');
                        if(countrySelect) {
                            countrySelect.addEventListener('change', function() {
                                var countryId = this.value;
                                stateSelect.innerHTML = '<option value="">All States</option>';
                                if(countryId) {
                                    fetch('/admin/state-by-country/' + countryId)
                                        .then(response => response.json())
                                        .then(data => {
                                            if(data.states) {
                                                // data.states is HTML string of <option>
                                                var tempDiv = document.createElement('div');
                                                tempDiv.innerHTML = data.states;
                                                var options = tempDiv.querySelectorAll('option');
                                                options.forEach(function(option) {
                                                    stateSelect.appendChild(option);
                                                });
                                            }
                                        });
                                }
                            });
                        }
                    });
                </script>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{__('admin.SN')}}</th>
                                    <th>District</th>
                                    <th>{{__('admin.State')}}</th>
                                    <th>{{__('admin.Country')}}</th>
                                    <th>{{__('admin.Action')}}</th>
                                  </tr>
                            </thead>
                            <tbody>
                                @foreach ($cities as $index => $city)
                                    <tr>
                                        <td>{{ $cities->firstItem() + $index }}</td>
                                        <td>{{ $city->name ?? ''}}</td>
                                        <td>{{ $city->state->name ?? '' }}</td>
                                        <td>{{ $city->state->country->name ?? ''}}</td>
                                        <td>
                                            <a href="{{ route('admin.city.edit',$city->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>


                                            @php
                                                $addressCount = \App\Models\Address::where('city_id', $city->id)->count();
                                            @endphp

                                            @if ($addressCount == 0)
                                                <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $city->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            @else
                                                <a href="javascript:;" data-toggle="modal" data-target="#canNotDeleteModal" class="btn btn-danger btn-sm" disabled><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            @endif

                                        </td>

                                    </tr>
                                  @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $cities->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                  </div>
                </div>
          </div>
        </section>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="canNotDeleteModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                      <div class="modal-body">
                          {{__('admin.You can not delete this city. Because there are one or more users and seller has been created in this city.')}}
                      </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('admin.Close')}}</button>
                </div>
            </div>
        </div>
    </div>

<script>
    function deleteData(id){
        $("#deleteForm").attr("action",'{{ url("admin/city/") }}'+"/"+id)
    }
</script>
@endsection
