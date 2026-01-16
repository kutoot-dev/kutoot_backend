@extends('admin.master_layout')
@section('title')
<title>Seller Application - {{ $application->application_id }}</title>
@endsection
@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Application Details</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admin.seller-applications.index') }}">Store Applications</a></div>
                <div class="breadcrumb-item">{{ $application->application_id }}</div>
            </div>
        </div>

        <div class="section-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible show fade">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert"><span>&times;</span></button>
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible show fade">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert"><span>&times;</span></button>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <div class="row">
                {{-- Application Details --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Application Information</h4>
                            <div class="card-header-action">
                                @switch($application->status)
                                    @case('PENDING')
                                        <span class="badge badge-warning badge-lg">Pending</span>
                                        @break
                                    @case('VERIFIED')
                                        <span class="badge badge-info badge-lg">Verified</span>
                                        @break
                                    @case('APPROVED')
                                        <span class="badge badge-success badge-lg">Approved</span>
                                        @break
                                    @case('REJECTED')
                                        <span class="badge badge-danger badge-lg">Rejected</span>
                                        @break
                                @endswitch
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="text-muted" width="40%">Application ID</td>
                                            <td><strong>{{ $application->application_id }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Store Name</td>
                                            <td><strong>{{ $application->store_name }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Owner Mobile</td>
                                            <td><a href="tel:{{ $application->owner_mobile }}">{{ $application->owner_mobile }}</a></td>
                                        </tr>
                                        @if($application->owner_email)
                                        <tr>
                                            <td class="text-muted">Owner Email</td>
                                            <td><a href="mailto:{{ $application->owner_email }}">{{ $application->owner_email }}</a></td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="text-muted">Store Type</td>
                                            <td>{{ $application->store_type }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Min Bill Amount</td>
                                            <td>₹{{ number_format($application->min_bill_amount, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="text-muted" width="40%">Applied On</td>
                                            <td>{{ $application->created_at->format('d M Y, h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Latitude</td>
                                            <td>{{ $application->lat }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Longitude</td>
                                            <td>{{ $application->lng }}</td>
                                        </tr>
                                        @if($application->seller_email)
                                        <tr>
                                            <td class="text-muted">Seller Email</td>
                                            <td>{{ $application->seller_email }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h6 class="text-muted mb-2">Store Address</h6>
                            <p class="mb-3">{{ $application->store_address }}</p>
                            
                            @if($application->lat && $application->lng)
                            <a href="https://www.google.com/maps?q={{ $application->lat }},{{ $application->lng }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-map-marker-alt"></i> View on Google Maps
                            </a>
                            @endif
                            
                            @if($application->verification_notes)
                            <hr>
                            <h6 class="text-muted mb-2">Verification Notes</h6>
                            <p class="mb-0">{{ $application->verification_notes }}</p>
                            <small class="text-muted">Verified on {{ $application->verified_at?->format('d M Y, h:i A') }}</small>
                            @endif
                            
                            @if($application->rejection_reason)
                            <hr>
                            <div class="alert alert-danger">
                                <h6 class="mb-2">Rejection Reason</h6>
                                <p class="mb-0">{{ $application->rejection_reason }}</p>
                                <small class="text-muted">Rejected on {{ $application->rejected_at?->format('d M Y, h:i A') }}</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Actions Panel --}}
                <div class="col-lg-4">
                    {{-- Verify Action --}}
                    @if($application->isPending())
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h4 class="text-white">Step 1: Verify Application</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">Call the seller and verify their store details before proceeding.</p>
                            <form action="{{ route('admin.seller-applications.verify', $application->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Verification Notes</label>
                                    <textarea name="verification_notes" class="form-control" rows="3" placeholder="E.g., Called seller, verified store exists, GST verified"></textarea>
                                </div>
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-check-circle"></i> Mark as Verified
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Approve/Reject Actions --}}
                    @if($application->isVerified())
                    <div class="card">
                        <div class="card-header bg-success">
                            <h4 class="text-white">Step 2: Approve Application</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">Approve to create seller account and send login credentials.</p>
                            <form action="{{ route('admin.seller-applications.approve', $application->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Seller Email <span class="text-danger">*</span></label>
                                    <input type="email" name="seller_email" class="form-control" required placeholder="seller@example.com" value="{{ $application->owner_email }}">
                                    <small class="text-muted">Credentials will be sent to this email</small>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-check"></i> Approve & Send Credentials
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-danger">
                            <h4 class="text-white">Or Reject Application</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.seller-applications.reject', $application->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Seller Email (Optional)</label>
                                    <input type="email" name="seller_email" class="form-control" placeholder="seller@example.com" value="{{ $application->owner_email }}">
                                    <small class="text-muted">Rejection reason will be sent to this email</small>
                                </div>
                                <div class="form-group">
                                    <label>Rejection Reason <span class="text-danger">*</span></label>
                                    <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="E.g., Store documents not valid / location mismatch"></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-times"></i> Reject Application
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Approved Status --}}
                    @if($application->isApproved())
                    <div class="card">
                        <div class="card-header bg-success">
                            <h4 class="text-white">Application Approved</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-success mb-2"><i class="fas fa-check-circle"></i> This seller has been approved and account created.</p>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Approved On</td>
                                    <td>{{ $application->approved_at?->format('d M Y, h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Seller Email</td>
                                    <td>{{ $application->seller_email }}</td>
                                </tr>
                                @if($application->seller)
                                <tr>
                                    <td class="text-muted">Seller Code</td>
                                    <td>{{ $application->seller->seller_code }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Rejected Status --}}
                    @if($application->isRejected())
                    <div class="card">
                        <div class="card-header bg-danger">
                            <h4 class="text-white">Application Rejected</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-danger"><i class="fas fa-times-circle"></i> This application has been rejected.</p>
                            <p class="text-muted">Rejected on {{ $application->rejected_at?->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Back Button --}}
                    <a href="{{ route('admin.seller-applications.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

