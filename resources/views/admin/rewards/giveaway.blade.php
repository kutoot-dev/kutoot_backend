@extends('admin.master_layout')
@section('title')
<title>{{__('Reward Giveaway')}}</title>
@endsection
@section('admin-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>{{__('Reward Giveaway')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{__('admin.Dashboard')}}</a></div>
                <div class="breadcrumb-item">{{__('Reward Giveaway')}}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{__('Distribute Coins & Coupons')}}</h4>
                        </div>
                        <div class="card-body">
                            <form id="giveaway-form" action="{{ route('admin.rewards.distribute') }}" method="POST">
                                @csrf

                                <!-- User Selection -->
                                <div class="form-group">
                                    <label>{{__('Select Users')}} <span class="text-danger">*</span></label>
                                    <select name="user_ids[]" id="user_ids" class="form-control" multiple required size="10">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }} - {{ $user->email }}
                                                @if($user->phone) ({{ $user->phone }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple users</small>
                                </div>

                                <!-- Campaign Selection -->
                                <div class="form-group">
                                    <label>{{__('Select Campaign')}} <span class="text-danger">*</span></label>
                                    <select name="campaign_id" id="campaign_id" class="form-control" required>
                                        <option value="">-- Select Campaign --</option>
                                        @foreach($campaigns as $campaign)
                                            <option value="{{ $campaign->id }}">{{ $campaign->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Base Plan Selection -->
                                <div class="form-group">
                                    <label>{{__('Select Base Plan')}} <span class="text-danger">*</span></label>
                                    <select name="baseplan_id" id="baseplan_id" class="form-control" required>
                                        <option value="">-- Select Base Plan --</option>
                                        @foreach($baseplans as $plan)
                                            <option value="{{ $plan->id }}"
                                                    data-coins="{{ $plan->coins_per_campaign }}"
                                                    data-coupons="{{ $plan->coupons_per_campaign }}">
                                                {{ $plan->title }}
                                                ({{ $plan->coins_per_campaign }} coins, {{ $plan->coupons_per_campaign }} coupons)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Quantity -->
                                <div class="form-group">
                                    <label>{{__('Quantity')}} <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" max="5" value="1" required>
                                    <small class="form-text text-muted">Number of plan iterations per user (Max: 5)</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-info btn-block" id="preview-btn">
                                            <i class="fas fa-eye"></i> Preview Coupons
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary btn-block" id="distribute-btn" disabled>
                                            <i class="fas fa-gift"></i> Distribute Rewards
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="row" id="preview-section" style="display: none;">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="text-white">{{__('Preview - Rewards Distribution')}}</h4>
                        </div>
                        <div class="card-body">
                            <div id="preview-content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    #user_ids {
        min-height: 200px;
        padding: 8px;
    }
    #user_ids option {
        padding: 8px 12px;
        border-bottom: 1px solid #e9ecef;
        cursor: pointer;
    }
    #user_ids option:hover {
        background-color: #f8f9fa;
    }
    #user_ids option:checked {
        background: linear-gradient(0deg, #4e73df 0%, #4e73df 100%);
        color: #fff;
    }
    .user-preview-card {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        background: #f8f9fc;
    }
    .user-preview-card h5 {
        color: #4e73df;
        margin-bottom: 15px;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 10px;
    }
    .coupon-code {
        background: #fff;
        border: 2px dashed #4e73df;
        border-radius: 5px;
        padding: 10px;
        margin: 5px;
        display: inline-block;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        font-weight: bold;
        color: #2e59d9;
    }
    .series-label {
        background: #1cc88a;
        color: #fff;
        border-radius: 3px;
        padding: 2px 8px;
        font-size: 12px;
        margin-left: 5px;
    }
    .summary-box {
        background: #4e73df;
        color: #fff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .summary-box .stat {
        text-align: center;
        padding: 10px;
    }
    .summary-box .stat h3 {
        margin: 0;
        font-size: 32px;
        color: #fff;
    }
    .summary-box .stat p {
        margin: 5px 0 0 0;
        opacity: 0.9;
        font-size: 14px;
    }
</style>

<script>
$(document).ready(function() {
    let previewData = null;

    // Preview Button Click
    $('#preview-btn').click(function() {
        const userIds = $('#user_ids').val();
        const campaignId = $('#campaign_id').val();
        const baseplanId = $('#baseplan_id').val();
        const quantity = $('#quantity').val();

        if (!userIds || userIds.length === 0) {
            toastr.error('Please select at least one user');
            return;
        }
        if (!campaignId) {
            toastr.error('Please select a campaign');
            return;
        }
        if (!baseplanId) {
            toastr.error('Please select a base plan');
            return;
        }
        if (!quantity || quantity < 1) {
            toastr.error('Please enter a valid quantity');
            return;
        }

        // Show loading
        $('#preview-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');

        $.ajax({
            url: '{{ route("admin.rewards.preview") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_ids: userIds,
                campaign_id: campaignId,
                baseplan_id: baseplanId,
                quantity: quantity
            },
            success: function(response) {
                if (response.success) {
                    previewData = response.data;
                    renderPreview(response.data);
                    $('#preview-section').slideDown();
                    $('#distribute-btn').prop('disabled', false);
                    toastr.success('Preview generated successfully');

                    // Scroll to preview
                    $('html, body').animate({
                        scrollTop: $('#preview-section').offset().top - 100
                    }, 500);
                } else {
                    toastr.error(response.message || 'Failed to generate preview');
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'An error occurred';
                toastr.error(error);
            },
            complete: function() {
                $('#preview-btn').prop('disabled', false).html('<i class="fas fa-eye"></i> Preview Coupons');
            }
        });
    });

    function renderPreview(data) {
        let html = '';

        // Summary Box
        html += `
            <div class="summary-box">
                <div class="row">
                    <div class="col-md-3 stat">
                        <h3>${data.summary.total_users}</h3>
                        <p>Total Users</p>
                    </div>
                    <div class="col-md-3 stat">
                        <h3>${data.summary.coins_per_user}</h3>
                        <p>Coins per User</p>
                    </div>
                    <div class="col-md-3 stat">
                        <h3>${data.summary.coupons_per_user}</h3>
                        <p>Coupons per User</p>
                    </div>
                    <div class="col-md-3 stat">
                        <h3>${data.summary.total_coupons}</h3>
                        <p>Total Coupons</p>
                    </div>
                </div>
            </div>
        `;

        // Campaign & Plan Info
        html += `
            <div class="alert alert-info">
                <strong>Campaign:</strong> ${data.campaign.title}<br>
                <strong>Base Plan:</strong> ${data.baseplan.title}<br>
                <strong>Quantity:</strong> ${data.quantity}x
            </div>
        `;

        // Campaign Limits
        if (data.campaign_limits && (data.campaign_limits.max_coins || data.campaign_limits.max_coupons)) {
            html += `
                <div class="row mb-3">
            `;

            if (data.campaign_limits.max_coins) {
                const coinsPercent = ((data.campaign_limits.distributed_coins + data.summary.total_coins) / data.campaign_limits.max_coins * 100).toFixed(1);
                html += `
                    <div class="col-md-6">
                        <div class="card bg-light border-warning">
                            <div class="card-body">
                                <h6 class="text-warning"><i class="fas fa-coins"></i> Campaign Coins Limit</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Max Coins:</span>
                                    <strong>${data.campaign_limits.max_coins.toLocaleString()}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Distributed:</span>
                                    <span>${data.campaign_limits.distributed_coins.toLocaleString()}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>This Giveaway:</span>
                                    <span class="text-primary">${data.summary.total_coins.toLocaleString()}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span><strong>Remaining After:</strong></span>
                                    <strong class="text-${data.campaign_limits.after_distribution_coins < 0 ? 'danger' : 'success'}">
                                        ${data.campaign_limits.after_distribution_coins.toLocaleString()}
                                    </strong>
                                </div>
                                <div class="progress mt-2" style="height: 20px;">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                         style="width: ${coinsPercent}%"
                                         aria-valuenow="${coinsPercent}" aria-valuemin="0" aria-valuemax="100">
                                        ${coinsPercent}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            if (data.campaign_limits.max_coupons) {
                const couponsPercent = ((data.campaign_limits.distributed_coupons + data.summary.total_coupons) / data.campaign_limits.max_coupons * 100).toFixed(1);
                html += `
                    <div class="col-md-6">
                        <div class="card bg-light border-success">
                            <div class="card-body">
                                <h6 class="text-success"><i class="fas fa-ticket-alt"></i> Campaign Coupons Limit</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Max Coupons:</span>
                                    <strong>${data.campaign_limits.max_coupons.toLocaleString()}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Distributed:</span>
                                    <span>${data.campaign_limits.distributed_coupons.toLocaleString()}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>This Giveaway:</span>
                                    <span class="text-primary">${data.summary.total_coupons.toLocaleString()}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span><strong>Remaining After:</strong></span>
                                    <strong class="text-${data.campaign_limits.after_distribution_coupons < 0 ? 'danger' : 'success'}">
                                        ${data.campaign_limits.after_distribution_coupons.toLocaleString()}
                                    </strong>
                                </div>
                                <div class="progress mt-2" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: ${couponsPercent}%"
                                         aria-valuenow="${couponsPercent}" aria-valuemin="0" aria-valuemax="100">
                                        ${couponsPercent}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            html += `
                </div>
            `;
        }

        // User-wise breakdown
        data.preview.forEach((item, index) => {
            html += `
                <div class="user-preview-card">
                    <h5>
                        <i class="fas fa-user"></i> ${item.user.name}
                        <small class="text-muted">(${item.user.email})</small>
                    </h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-coins text-warning"></i> Coins:</strong>
                            <span class="badge badge-warning">${item.coins}</span>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-ticket-alt text-success"></i> Coupons:</strong>
                            <span class="badge badge-success">${item.coupons.length}</span>
                        </div>
                    </div>

                    <div>
                        <strong>Coupon Codes:</strong><br>
            `;

            item.coupons.forEach((coupon, i) => {
                html += `
                    <span class="coupon-code">
                        ${coupon.code}
                        ${coupon.series_label ? `<span class="series-label">${coupon.series_label}</span>` : ''}
                    </span>
                `;
                if ((i + 1) % 3 === 0) {
                    html += '<br>';
                }
            });

            html += `
                    </div>
                </div>
            `;
        });

        $('#preview-content').html(html);
    }

    // Form submission confirmation
    $('#giveaway-form').submit(function(e) {
        if (!previewData) {
            e.preventDefault();
            toastr.error('Please preview the rewards before distribution');
            return false;
        }

        return confirm('Are you sure you want to distribute these rewards? This action cannot be undone.');
    });

    // Reset preview when form changes
    $('#user_ids, #campaign_id, #baseplan_id, #quantity').change(function() {
        $('#preview-section').slideUp();
        $('#distribute-btn').prop('disabled', true);
        previewData = null;
    });
});
</script>
@endsection
