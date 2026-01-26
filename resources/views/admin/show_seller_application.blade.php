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
                <div class="col-12 col-lg-8">
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
                        <div class="card-body" style="overflow-x: auto;">
                            {{-- Basic Store Information --}}
                            <h6 class="text-primary mb-3"><i class="fas fa-store"></i> Store Information</h6>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="table-responsive">
                                    <table class="table table-sm table-borderless" style="word-break: break-word;">
                                        <tr>
                                            <td class="text-muted" style="width: 40%; min-width: 100px;">Application ID</td>
                                            <td><strong>{{ $application->application_id }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Store Name</td>
                                            <td><strong>{{ $application->store_name }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Store Type</td>
                                            <td>{{ $application->store_type ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Min Bill Amount</td>
                                            <td>₹{{ number_format($application->min_bill_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Applied On</td>
                                            <td>{{ $application->created_at->format('d M Y, h:i A') }}</td>
                                        </tr>
                                    </table>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="table-responsive">
                                    <table class="table table-sm table-borderless" style="word-break: break-word;">
                                        <tr>
                                            <td class="text-muted" style="width: 40%; min-width: 100px;">Owner Mobile</td>
                                            <td><a href="tel:{{ $application->owner_mobile }}">{{ $application->owner_mobile }}</a></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Owner Email</td>
                                            <td>
                                                @if($application->owner_email)
                                                    <a href="mailto:{{ $application->owner_email }}">{{ $application->owner_email }}</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @if($application->seller_email)
                                        <tr>
                                            <td class="text-muted">Seller Email</td>
                                            <td><a href="mailto:{{ $application->seller_email }}">{{ $application->seller_email }}</a></td>
                                        </tr>
                                        @endif
                                        @if($application->isApproved() && $application->seller)
                                        <tr>
                                            <td class="text-muted">Seller Code</td>
                                            <td><strong>{{ $application->seller->seller_code ?? '-' }}</strong></td>
                                        </tr>
                                        @endif
                                    </table>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            {{-- GST Details --}}
                            <h6 class="text-primary mb-3"><i class="fas fa-file-invoice"></i> GST Details</h6>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                    <table class="table table-sm table-borderless" style="word-break: break-word;">
                                        <tr>
                                            <td class="text-muted" style="width: 20%; min-width: 120px;">GST Number</td>
                                            <td><strong>{{ $application->gst_number ?? 'Not Provided' }}</strong></td>
                                        </tr>
                                    </table>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            {{-- Location Details --}}
                            <h6 class="text-primary mb-3"><i class="fas fa-map-marker-alt"></i> Location Details</h6>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="table-responsive">
                                    <table class="table table-sm table-borderless" style="word-break: break-word;">
                                        <tr>
                                            <td class="text-muted" style="width: 40%; min-width: 100px;">Country</td>
                                            <td>{{ $application->country ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">State</td>
                                            <td>{{ $application->state ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">City</td>
                                            <td>{{ $application->city ?? '-' }}</td>
                                        </tr>
                                    </table>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="table-responsive">
                                    <table class="table table-sm table-borderless" style="word-break: break-word;">
                                        <tr>
                                            <td class="text-muted" style="width: 40%; min-width: 100px;">Latitude</td>
                                            <td>{{ $application->lat ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Longitude</td>
                                            <td>{{ $application->lng ?? '-' }}</td>
                                        </tr>
                                    </table>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 mb-3">
                                <h6 class="text-muted mb-2">Full Address</h6>
                                <p class="mb-2 p-2 bg-light rounded">{{ $application->store_address ?? 'Not Provided' }}</p>
                                @if($application->lat && $application->lng)
                                <a href="https://www.google.com/maps?q={{ $application->lat }},{{ $application->lng }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-map-marker-alt"></i> View on Google Maps
                                </a>
                                @endif
                            </div>

                            <hr>

                            {{-- Bank Account Details --}}
                            <h6 class="text-primary mb-3"><i class="fas fa-university"></i> Bank Account Details</h6>
                            @if($application->bank_name || $application->account_number || $application->ifsc_code || $application->beneficiary_name || $application->upi_id)
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="table-responsive">
                                    <table class="table table-sm table-borderless" style="word-break: break-word;">
                                        <tr>
                                            <td class="text-muted" style="width: 45%; min-width: 100px;">Bank Name</td>
                                            <td><strong>{{ $application->bank_name ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Account Number</td>
                                            <td><strong>{{ $application->account_number ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">IFSC Code</td>
                                            <td><strong>{{ $application->ifsc_code ?? '-' }}</strong></td>
                                        </tr>
                                    </table>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="table-responsive">
                                    <table class="table table-sm table-borderless" style="word-break: break-word;">
                                        <tr>
                                            <td class="text-muted" style="width: 45%; min-width: 100px;">Beneficiary Name</td>
                                            <td><strong>{{ $application->beneficiary_name ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">UPI ID</td>
                                            <td><strong>{{ $application->upi_id ?? '-' }}</strong></td>
                                        </tr>
                                    </table>
                                    </div>
                                </div>
                            </div>
                            @else
                            <p class="text-muted">No bank details provided</p>
                            @endif

                            {{-- Commission & Settings (for approved apps) --}}
                            @if($application->isApproved() && ($application->commission_percent || $application->discount_percent || $application->rating))
                            <hr>
                            <h6 class="text-primary mb-3"><i class="fas fa-cog"></i> Commission & Discount Settings</h6>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                    <table class="table table-sm table-borderless" style="word-break: break-word;">
                                        <tr>
                                            <td class="text-muted" style="width: 20%; min-width: 120px;">Commission</td>
                                            <td><strong>{{ $application->commission_percent ?? '0' }}%</strong></td>
                                            <td class="text-muted" style="width: 20%; min-width: 120px;">Discount</td>
                                            <td><strong>{{ $application->discount_percent ?? '0' }}%</strong></td>
                                            <td class="text-muted" style="width: 15%; min-width: 80px;">Rating</td>
                                            <td><strong>{{ $application->rating ?? '0' }} <i class="fas fa-star text-warning"></i></strong></td>
                                        </tr>
                                    </table>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Store Images --}}
                            @php
                                $allImages = [];
                                if ($application->store_image) {
                                    $allImages[] = $application->store_image;
                                }
                                $additionalImages = $application->images ?? [];
                                if (is_string($additionalImages)) {
                                    $additionalImages = json_decode($additionalImages, true) ?? [];
                                }
                                $allImages = array_merge($allImages, $additionalImages);
                            @endphp

                            <hr>
                            <h6 class="text-primary mb-3"><i class="fas fa-images"></i> Store Images</h6>
                            @if(count($allImages) > 0)
                            <div class="row">
                                @foreach($allImages as $index => $image)
                                <div class="col-6 col-md-4 col-lg-3 mb-3">
                                    <a href="{{ asset($image) }}" target="_blank">
                                        <img src="{{ asset($image) }}" alt="Store Image {{ $index + 1 }}" class="img-thumbnail" style="width: 100%; height: 120px; object-fit: cover;">
                                    </a>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-muted">No images uploaded</p>
                            @endif

                            {{-- Application Status Timeline --}}
                            @if($application->verification_notes || $application->isVerified() || $application->isApproved() || $application->isRejected())
                            <hr>
                            <h6 class="text-primary mb-3"><i class="fas fa-history"></i> Application Timeline</h6>

                            @if($application->verification_notes || $application->verified_at)
                            <div class="alert alert-info mb-2">
                                <strong><i class="fas fa-check-circle"></i> Verified</strong>
                                @if($application->verified_at)
                                <small class="float-right">{{ $application->verified_at->format('d M Y, h:i A') }}</small>
                                @endif
                                @if($application->verifier)
                                <br><small class="text-muted">By: {{ $application->verifier->name ?? 'Admin' }}</small>
                                @endif
                                @if($application->verification_notes)
                                <p class="mb-0 mt-2">{{ $application->verification_notes }}</p>
                                @endif
                            </div>
                            @endif

                            @if($application->isApproved())
                            <div class="alert alert-success mb-2">
                                <strong><i class="fas fa-check"></i> Approved</strong>
                                @if($application->approved_at)
                                <small class="float-right">{{ $application->approved_at->format('d M Y, h:i A') }}</small>
                                @endif
                                @if($application->approver)
                                <br><small class="text-muted">By: {{ $application->approver->name ?? 'Admin' }}</small>
                                @endif
                                @if($application->seller_email)
                                <p class="mb-0 mt-2">Credentials sent to: <strong>{{ $application->seller_email }}</strong></p>
                                @endif
                            </div>
                            @endif

                            @if($application->rejection_reason || $application->isRejected())
                            <div class="alert alert-danger mb-2">
                                <strong><i class="fas fa-times"></i> Rejected</strong>
                                @if($application->rejected_at)
                                <small class="float-right">{{ $application->rejected_at->format('d M Y, h:i A') }}</small>
                                @endif
                                @if($application->rejecter)
                                <br><small class="text-muted">By: {{ $application->rejecter->name ?? 'Admin' }}</small>
                                @endif
                                @if($application->rejection_reason)
                                <p class="mb-0 mt-2"><strong>Reason:</strong> {{ $application->rejection_reason }}</p>
                                @endif
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions Panel --}}
                <div class="col-12 col-lg-4">
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

                                <hr>
                                <h6 class="text-muted mb-3">Commission & Discount Settings</h6>

                                <div class="form-group">
                                    <label>Commission Fee (%)</label>
                                    <input type="number" name="commission_percent" class="form-control" min="0" max="100" step="0.1" placeholder="e.g., 10" value="10">
                                    <small class="text-muted">Percentage deducted from each transaction</small>
                                </div>

                                <div class="form-group">
                                    <label>Discount for Users (%)</label>
                                    <input type="number" name="discount_percent" class="form-control" min="0" max="100" step="0.1" placeholder="e.g., 5" value="0">
                                    <small class="text-muted">Discount offered to users at this store</small>
                                </div>

                                <div class="form-group">
                                    <label>Initial Rating</label>
                                    <input type="number" name="rating" class="form-control" min="0" max="5" step="0.1" placeholder="e.g., 4.0" value="0">
                                    <small class="text-muted">Admin-set initial rating (0-5)</small>
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

                    {{-- Edit Application Button --}}
                    @if(!$application->isApproved())
                    <a href="{{ route('admin.seller-applications.edit', $application->id) }}" class="btn btn-info btn-block mb-3">
                        <i class="fas fa-edit"></i> Edit Application
                    </a>
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

