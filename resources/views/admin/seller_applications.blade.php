@extends('admin.master_layout')
@section('title')
<title>{{__('admin.Store Applications')}}</title>
@endsection
@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{__('admin.Store Applications')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
                <div class="breadcrumb-item">{{__('admin.Store Applications')}}</div>
            </div>
        </div>

        <div class="section-body">
            {{-- Status Filter Tabs --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link {{ !$status ? 'active' : '' }}" href="{{ route('admin.seller-applications.index') }}">
                                        All <span class="badge badge-primary">{{ $statusCounts['all'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $status == 'pending' ? 'active' : '' }}" href="{{ route('admin.seller-applications.index', ['status' => 'pending']) }}">
                                        Pending <span class="badge badge-warning">{{ $statusCounts['pending'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $status == 'verified' ? 'active' : '' }}" href="{{ route('admin.seller-applications.index', ['status' => 'verified']) }}">
                                        Verified <span class="badge badge-info">{{ $statusCounts['verified'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $status == 'approved' ? 'active' : '' }}" href="{{ route('admin.seller-applications.index', ['status' => 'approved']) }}">
                                        Approved <span class="badge badge-success">{{ $statusCounts['approved'] }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $status == 'rejected' ? 'active' : '' }}" href="{{ route('admin.seller-applications.index', ['status' => 'rejected']) }}">
                                        Rejected <span class="badge badge-danger">{{ $statusCounts['rejected'] }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Applications Table --}}
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive table-invoice">
                                <table class="table table-striped" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th>{{__('admin.SN')}}</th>
                                            <th>Application ID</th>
                                            <th>Store Name</th>
                                            <th>Mobile</th>
                                            <th>Store Type</th>
                                            <th>Min Bill Amount</th>
                                            <th>Status</th>
                                            <th>Applied On</th>
                                            <th>{{__('admin.Action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($applications as $index => $app)
                                            <tr>
                                                <td>{{ $applications->firstItem() + $index }}</td>
                                                <td><strong>{{ $app->application_id }}</strong></td>
                                                <td>{{ $app->store_name }}</td>
                                                <td>{{ $app->owner_mobile }}</td>
                                                <td>{{ $app->store_type }}</td>
                                                <td>₹{{ number_format($app->min_bill_amount, 2) }}</td>
                                                <td>
                                                    @switch($app->status)
                                                        @case('PENDING')
                                                            <span class="badge badge-warning">Pending</span>
                                                            @break
                                                        @case('VERIFIED')
                                                            <span class="badge badge-info">Verified</span>
                                                            @break
                                                        @case('APPROVED')
                                                            <span class="badge badge-success">Approved</span>
                                                            @break
                                                        @case('REJECTED')
                                                            <span class="badge badge-danger">Rejected</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>{{ $app->created_at->format('d M Y, h:i A') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.seller-applications.show', $app->id) }}" class="btn btn-primary btn-sm" title="View Details">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($app->status != 'APPROVED')
                                                        <a href="javascript:;" data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" onclick="deleteData({{ $app->id }})" title="Delete">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4">No applications found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            {{-- Pagination --}}
                            <div class="mt-4">
                                {{ $applications->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">{{__('admin.Delete Confirmation')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{__('admin.Are you sure you want to delete this application?')}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('admin.Close')}}</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{__('admin.Delete')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function deleteData(id) {
        document.getElementById('deleteForm').action = '{{ url("admin/seller-applications") }}/' + id;
    }
</script>
@endsection

