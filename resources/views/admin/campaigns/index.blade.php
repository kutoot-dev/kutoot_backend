@extends('admin.master_layout')
@section('title')
<title>{{__('Campaigns')}}</title>
@endsection
@section('admin-content')
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>{{__('Campaigns')}}</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
              <div class="breadcrumb-item">{{__('admin.campaingns')}}</div>
            </div>
          </div>

          <div class="section-body">
            <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{__('admin.campaigns.create')}}</a>
            <div class="row mt-4">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                     @php
                       echo bcrypt('1234');
                     @endphp
                      </div>
                      <div class="table-responsive table-invoice">
                        <table class="table table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Campaign ID</th>
                <th>Name</th>
                <th>Prefix</th>
                <th>Range</th>
                <th>Pick Count</th>
                <th>Tickets Issued</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
                            </thead>
                            <tbody>
                           
                                   @forelse($campaigns as $campaign)
            <tr>
                <td>{{ $campaign->id }}</td>
                <td>{{ $campaign->name }}</td>
                <td>{{ $campaign->series_prefix }}</td>
                <td>{{ $campaign->number_min }}–{{ $campaign->number_max }}</td>
                <td>{{ $campaign->numbers_per_ticket }}</td>
                <td>{{ $campaign->tickets_issued }}</td>
                <td>
                    @if($campaign->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </td>
                <td>
                    {{-- <a href="{{ route('admin.campaigns.show', $campaign->id) }}" class="btn btn-sm btn-info">View</a> --}}
                    <a href="{{ route('admin.campaigns.edit', $campaign->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.campaigns.destroy', $campaign->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this campaign?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7">No campaigns found.</td></tr>
        @endforelse
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
