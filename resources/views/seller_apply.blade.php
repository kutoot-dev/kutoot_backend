<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Become a Seller - Kutoot</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('backend/css/store-shadcn.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--sh-background);
            color: var(--sh-foreground);
            min-height: 100vh;
            padding: 2rem 1rem;
            line-height: 1.5;
        }
        
        .seller-form-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .seller-form-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .seller-form-header .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .seller-form-header .logo img {
            height: 40px;
        }
        
        .seller-form-header .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--sh-primary);
        }
        
        .seller-form-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--sh-foreground);
            margin-bottom: 0.5rem;
        }
        
        .seller-form-header p {
            color: var(--sh-muted-foreground);
            font-size: 0.875rem;
        }
        
        .card {
            background-color: var(--sh-card);
            border: 1px solid var(--sh-border);
            border-radius: var(--sh-radius-lg);
            box-shadow: var(--sh-shadow);
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section:last-child {
            margin-bottom: 0;
        }
        
        .form-section-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--sh-foreground);
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--sh-border);
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-group:last-child {
            margin-bottom: 0;
        }
        
        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--sh-foreground);
            margin-bottom: 0.5rem;
        }
        
        .required {
            color: var(--sh-destructive);
            margin-left: 0.25rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            border: 1px solid var(--sh-input);
            border-radius: var(--sh-radius);
            background-color: var(--sh-input-bg);
            color: var(--sh-foreground);
            transition: all 0.15s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--sh-ring);
            background-color: var(--sh-card);
            box-shadow: 0 0 0 3px rgba(234, 107, 30, 0.1);
        }
        
        .form-control.error {
            border-color: var(--sh-destructive);
        }
        
        .form-control.success {
            border-color: var(--sh-success);
        }
        
        .form-control::placeholder {
            color: var(--sh-muted-foreground);
        }
        
        select.form-control {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236B5D52' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.875rem center;
            padding-right: 2.5rem;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-hint {
            font-size: 0.75rem;
            color: var(--sh-muted-foreground);
            margin-top: 0.375rem;
        }
        
        .form-error {
            font-size: 0.75rem;
            color: var(--sh-destructive);
            margin-top: 0.375rem;
            display: none;
        }
        
        .form-error.show {
            display: block;
        }
        
        .form-success {
            font-size: 0.75rem;
            color: var(--sh-success);
            margin-top: 0.375rem;
            display: none;
        }
        
        .form-success.show {
            display: block;
        }
        
        .phone-input-group {
            display: flex;
            gap: 0.75rem;
        }
        
        .phone-input-group .form-control {
            flex: 1;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: var(--sh-radius);
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        
        .btn-primary {
            background-color: var(--sh-primary);
            color: var(--sh-primary-foreground);
        }
        
        .btn-primary:hover:not(:disabled) {
            background-color: var(--sh-primary-hover);
        }
        
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .btn-success {
            background-color: var(--sh-success);
            color: var(--sh-success-foreground);
        }
        
        .btn-success:hover:not(:disabled) {
            opacity: 0.9;
        }
        
        .btn-secondary {
            background-color: var(--sh-secondary);
            color: var(--sh-secondary-foreground);
        }
        
        .btn-secondary:hover:not(:disabled) {
            background-color: var(--sh-secondary-hover);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .btn.loading {
            position: relative;
            color: transparent;
        }
        
        .btn.loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            border: 2px solid currentColor;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .otp-group {
            display: flex;
            gap: 0.75rem;
            margin-top: 0.75rem;
            display: none;
        }
        
        .otp-group.show {
            display: flex;
        }
        
        .otp-group .form-control {
            flex: 1;
            text-align: center;
            letter-spacing: 0.5rem;
            font-size: 1.125rem;
            font-weight: 600;
        }
        
        .location-section {
            background-color: var(--sh-muted);
            border: 1px solid var(--sh-border);
            border-radius: var(--sh-radius);
            padding: 1.5rem;
            margin-top: 1rem;
        }
        
        .location-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        
        .location-header h3 {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--sh-foreground);
        }
        
        .coords-display {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
            flex-wrap: wrap;
            display: none;
        }
        
        .coords-display.show {
            display: flex;
        }
        
        .coord-badge {
            padding: 0.5rem 1rem;
            background-color: var(--sh-card);
            border: 1px solid var(--sh-border);
            border-radius: var(--sh-radius);
            font-size: 0.75rem;
            color: var(--sh-muted-foreground);
        }
        
        .coord-badge strong {
            color: var(--sh-primary);
            margin-right: 0.25rem;
        }
        
        .manual-location {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--sh-border);
            display: none;
        }
        
        .manual-location.show {
            display: block;
        }
        
        .manual-location-grid {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 0.75rem;
            align-items: end;
        }
        
        .alert {
            padding: 0.875rem 1rem;
            border-radius: var(--sh-radius);
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            border: 1px solid;
        }
        
        .alert-danger {
            background-color: rgba(193, 39, 45, 0.1);
            border-color: var(--sh-destructive);
            color: var(--sh-destructive);
        }
        
        .alert-success {
            background-color: rgba(49, 215, 169, 0.1);
            border-color: var(--sh-success);
            color: var(--sh-success);
        }
        
        .submit-section {
            text-align: right;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 1rem 0.5rem;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .phone-input-group {
                flex-direction: column;
            }
            
            .location-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .location-header .btn {
                width: 100%;
            }
            
            .manual-location-grid {
                grid-template-columns: 1fr;
            }
            
            .submit-section {
                text-align: center;
            }
            
            .submit-section .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="seller-form-container">
        <div class="seller-form-header">
            <div class="logo">
                <img src="{{ asset('uploads/website-images/logo.png') }}" alt="Kutoot" onerror="this.style.display='none'">
                <span class="logo-text">KUTOOT</span>
            </div>
            <h1>Become a Seller</h1>
            <p>Join our platform and start selling today</p>
        </div>
        
        <div class="card">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form id="sellerApplyForm" action="{{ route('seller.apply.submit') }}" method="POST" novalidate>
                @csrf
                
                <div class="form-section">
                    <div class="form-section-title">Basic Information</div>
                    
                    <div class="form-group">
                        <label>
                            Store Name <span class="required">*</span>
                        </label>
                        <input type="text" name="store_name" id="store_name" class="form-control" 
                               value="{{ old('store_name') }}" 
                               placeholder="Enter your store name"
                               required>
                        <div class="form-error" id="store_name_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            Owner Mobile Number <span class="required">*</span>
                        </label>
                        <div class="phone-input-group">
                            <input type="tel" id="owner_mobile" name="owner_mobile" class="form-control" 
                                   placeholder="10-digit mobile number" pattern="[0-9]{10}" maxlength="10" 
                                   value="{{ old('owner_mobile') }}" required>
                            <button type="button" id="btnSendOtp" class="btn btn-primary">Send OTP</button>
                        </div>
                        <div class="form-hint">We'll send a verification code to this number</div>
                        <div class="form-error" id="owner_mobile_error"></div>
                        <div class="otp-group" id="otpGroup">
                            <input type="text" id="otpInput" class="form-control" placeholder="Enter OTP" maxlength="6" pattern="[0-9]{6}">
                            <button type="button" id="btnVerifyOtp" class="btn btn-primary">Verify</button>
                        </div>
                        <div class="form-error" id="otp_error"></div>
                        <div class="form-success" id="otp_success"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            Store Category <span class="required">*</span>
                        </label>
                        <select name="store_type" id="store_type" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->name }}" {{ old('store_type') == $category->name ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-error" id="store_type_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            Minimum Bill Amount (₹) <span class="required">*</span>
                        </label>
                        <input type="number" name="min_bill_amount" id="min_bill_amount" class="form-control" 
                               value="{{ old('min_bill_amount', 500) }}" min="0" step="10"
                               placeholder="e.g., 500" required>
                        <div class="form-hint">Minimum order amount to qualify for discounts</div>
                        <div class="form-error" id="min_bill_amount_error"></div>
                    </div>
                </div>
                
                <div class="form-section">
                    <div class="form-section-title">Store Location</div>
                    
                    <div class="location-section">
                        <div class="location-header">
                            <h3>Location <span class="required">*</span></h3>
                            <button type="button" id="btnUseLocation" class="btn btn-secondary">
                                📍 Use Current Location
                            </button>
                        </div>
                        
                        <div class="form-group">
                            <label>Search Store Name / Area</label>
                            <input type="text" id="locationSearch" class="form-control" 
                                   placeholder="Type store name or area">
                            <div class="form-hint">Start typing to see suggestions. If not found, use location button or enter coordinates manually.</div>
                        </div>
                        
                        <div class="coords-display" id="coordsDisplay">
                            <div class="coord-badge"><strong>Lat:</strong> <span id="latDisplay">—</span></div>
                            <div class="coord-badge"><strong>Lng:</strong> <span id="lngDisplay">—</span></div>
                        </div>
                        
                        <div class="manual-location" id="manualLocation">
                            <div class="form-group">
                                <label>Enter Coordinates Manually</label>
                                <div class="manual-location-grid">
                                    <div>
                                        <input type="number" step="any" id="manualLat" class="form-control" 
                                               placeholder="Latitude">
                                    </div>
                                    <div>
                                        <input type="number" step="any" id="manualLng" class="form-control" 
                                               placeholder="Longitude">
                                    </div>
                                    <button type="button" id="btnSetManualLocation" class="btn btn-primary">Set</button>
                                </div>
                                <div class="form-hint">Get coordinates from Google Maps → Right-click on location → Copy coordinates</div>
                            </div>
                        </div>
                        
                        <a href="javascript:;" id="toggleManual" style="color: var(--sh-primary); text-decoration: none; font-size: 0.875rem; margin-top: 0.75rem; display: inline-block;">
                            Enter coordinates manually
                        </a>
                        
                        <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
                        <input type="hidden" name="lng" id="lng" value="{{ old('lng') }}">
                        <div class="form-error" id="location_error"></div>
                        <div class="form-success" id="location_success"></div>
                    </div>
                </div>
                
                <div class="form-section">
                    <div class="form-section-title">Store Address</div>
                    
                    <div class="form-group">
                        <label>
                            Complete Address <span class="required">*</span>
                        </label>
                        <textarea name="store_address" id="store_address" class="form-control" rows="4" 
                                  placeholder="Enter complete store address including street, area, city, and pincode"
                                  required>{{ old('store_address') }}</textarea>
                        <div class="form-hint">Include street number, area, city, state, and pincode</div>
                        <div class="form-error" id="store_address_error"></div>
                    </div>
                </div>
                
                <input type="hidden" id="otpVerified" value="0">
                
                <div class="submit-section">
                    <button type="submit" id="btnSubmit" class="btn btn-primary" disabled>
                        Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let otpVerified = false;
        
        function showError(fieldId, message) {
            const errorEl = document.getElementById(fieldId + '_error');
            const field = document.getElementById(fieldId);
            if (errorEl) {
                errorEl.textContent = message;
                errorEl.classList.add('show');
            }
            if (field) {
                field.classList.add('error');
                field.classList.remove('success');
            }
        }
        
        function showSuccess(fieldId, message) {
            const successEl = document.getElementById(fieldId + '_success');
            const field = document.getElementById(fieldId);
            if (successEl) {
                successEl.textContent = message;
                successEl.classList.add('show');
            }
            if (field) {
                field.classList.add('success');
                field.classList.remove('error');
            }
        }
        
        function hideFeedback(fieldId) {
            const errorEl = document.getElementById(fieldId + '_error');
            const successEl = document.getElementById(fieldId + '_success');
            if (errorEl) errorEl.classList.remove('show');
            if (successEl) successEl.classList.remove('show');
        }
        
        function checkFormValidity() {
            const lat = document.getElementById('lat').value;
            const lng = document.getElementById('lng').value;
            const storeName = document.getElementById('store_name').value.trim();
            const storeType = document.getElementById('store_type').value;
            const storeAddress = document.getElementById('store_address').value.trim();
            const minBillAmount = document.getElementById('min_bill_amount').value;
            const btnSubmit = document.getElementById('btnSubmit');
            
            const isValid = otpVerified && 
                           lat && lng && 
                           storeName.length >= 3 && 
                           storeType && 
                           storeAddress.length >= 10 &&
                           minBillAmount && parseFloat(minBillAmount) >= 0;
            
            btnSubmit.disabled = !isValid;
        }
        
        // Field validations
        document.getElementById('store_name').addEventListener('blur', function() {
            if (this.value.trim().length < 3) {
                showError('store_name', 'Store name must be at least 3 characters');
            } else {
                hideFeedback('store_name');
                this.classList.remove('error');
                this.classList.add('success');
            }
            checkFormValidity();
        });
        
        document.getElementById('owner_mobile').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length === 10) {
                hideFeedback('owner_mobile');
                this.classList.remove('error');
                this.classList.add('success');
            } else if (this.value.length > 0) {
                showError('owner_mobile', 'Enter 10-digit mobile number');
            }
        });
        
        document.getElementById('store_type').addEventListener('change', function() {
            if (this.value) {
                hideFeedback('store_type');
                this.classList.remove('error');
                this.classList.add('success');
            }
            checkFormValidity();
        });
        
        document.getElementById('min_bill_amount').addEventListener('blur', function() {
            const value = parseFloat(this.value);
            if (isNaN(value) || value < 0) {
                showError('min_bill_amount', 'Enter a valid amount (minimum 0)');
            } else {
                hideFeedback('min_bill_amount');
                this.classList.remove('error');
                this.classList.add('success');
            }
            checkFormValidity();
        });
        
        document.getElementById('store_address').addEventListener('blur', function() {
            if (this.value.trim().length < 10) {
                showError('store_address', 'Please enter a complete address (at least 10 characters)');
            } else {
                hideFeedback('store_address');
                this.classList.remove('error');
                this.classList.add('success');
            }
            checkFormValidity();
        });
        
        // Send OTP
        document.getElementById('btnSendOtp').addEventListener('click', async function() {
            const phone = document.getElementById('owner_mobile').value;
            if (!/^[0-9]{10}$/.test(phone)) {
                showError('owner_mobile', 'Please enter a valid 10-digit mobile number');
                return;
            }
            
            this.classList.add('loading');
            this.disabled = true;
            const originalText = this.textContent;
            
            try {
                const response = await fetch('{{ route("seller.apply.send-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ phone })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('otpGroup').classList.add('show');
                    document.getElementById('otpInput').focus();
                    this.textContent = 'Resend OTP';
                    this.disabled = false;
                    this.classList.remove('loading');
                    showSuccess('owner_mobile', 'OTP sent successfully!');
                    
                    if (data.debug_otp) {
                        showSuccess('otp', 'Debug OTP: ' + data.debug_otp);
                    }
                } else {
                    showError('owner_mobile', data.message || 'Failed to send OTP');
                    this.disabled = false;
                    this.textContent = originalText;
                    this.classList.remove('loading');
                }
            } catch (err) {
                showError('owner_mobile', 'Network error. Please try again.');
                this.disabled = false;
                this.textContent = originalText;
                this.classList.remove('loading');
            }
        });
        
        // Verify OTP
        document.getElementById('btnVerifyOtp').addEventListener('click', async function() {
            const phone = document.getElementById('owner_mobile').value;
            const otp = document.getElementById('otpInput').value;
            
            if (!/^[0-9]{6}$/.test(otp)) {
                showError('otp', 'Please enter a valid 6-digit OTP');
                return;
            }
            
            this.classList.add('loading');
            this.disabled = true;
            const originalText = this.textContent;
            
            try {
                const response = await fetch('{{ route("seller.apply.verify-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ phone, otp })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    otpVerified = true;
                    document.getElementById('otpVerified').value = '1';
                    document.getElementById('btnSendOtp').textContent = '✓ Verified';
                    document.getElementById('btnSendOtp').classList.remove('btn-primary');
                    document.getElementById('btnSendOtp').classList.add('btn-success');
                    document.getElementById('btnSendOtp').disabled = true;
                    document.getElementById('owner_mobile').readOnly = true;
                    document.getElementById('otpGroup').style.display = 'none';
                    showSuccess('otp', 'Mobile number verified successfully');
                    checkFormValidity();
                } else {
                    showError('otp', data.message || 'Invalid OTP');
                    document.getElementById('otpInput').value = '';
                    document.getElementById('otpInput').focus();
                    this.disabled = false;
                    this.textContent = originalText;
                    this.classList.remove('loading');
                }
            } catch (err) {
                showError('otp', 'Network error. Please try again.');
                this.disabled = false;
                this.textContent = originalText;
                this.classList.remove('loading');
            }
        });
        
        document.getElementById('otpInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('btnVerifyOtp').click();
            }
        });
        
        // Use Current Location
        document.getElementById('btnUseLocation').addEventListener('click', function() {
            if (!navigator.geolocation) {
                showError('location', 'Geolocation not supported. Please use manual entry.');
                document.getElementById('manualLocation').classList.add('show');
                return;
            }
            
            const btn = this;
            btn.textContent = '📍 Getting location...';
            btn.disabled = true;
            hideFeedback('location');
            
            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const lat = position.coords.latitude.toFixed(7);
                    const lng = position.coords.longitude.toFixed(7);
                    
                    document.getElementById('lat').value = lat;
                    document.getElementById('lng').value = lng;
                    document.getElementById('latDisplay').textContent = lat;
                    document.getElementById('lngDisplay').textContent = lng;
                    document.getElementById('coordsDisplay').classList.add('show');
                    
                    btn.textContent = '📍 Fetching address...';
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`, {
                            headers: { 'Accept-Language': 'en' }
                        });
                        const data = await response.json();
                        
                        if (data && data.display_name) {
                            document.getElementById('store_address').value = data.display_name;
                        }
                    } catch (err) {
                        console.log('Reverse geocoding failed:', err);
                    }
                    
                    btn.innerHTML = '✅ Location Set';
                    btn.classList.remove('btn-secondary');
                    btn.classList.add('btn-success');
                    btn.disabled = false;
                    showSuccess('location', 'Location captured successfully');
                    checkFormValidity();
                },
                (error) => {
                    let message = 'Unable to get location.';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            message = 'Location access denied. Please enable location permission.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = 'Location unavailable. Please check GPS settings.';
                            break;
                        case error.TIMEOUT:
                            message = 'Location request timed out. Please try again.';
                            break;
                    }
                    showError('location', message);
                    btn.textContent = '📍 Use Current Location';
                    btn.disabled = false;
                    document.getElementById('manualLocation').classList.add('show');
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        });
        
        // Toggle Manual Location
        document.getElementById('toggleManual').addEventListener('click', function(e) {
            e.preventDefault();
            const manual = document.getElementById('manualLocation');
            if (manual.classList.contains('show')) {
                manual.classList.remove('show');
                this.textContent = 'Enter coordinates manually';
            } else {
                manual.classList.add('show');
                this.textContent = 'Hide manual entry';
            }
        });
        
        // Set Manual Location
        document.getElementById('btnSetManualLocation').addEventListener('click', function() {
            const manualLat = document.getElementById('manualLat').value;
            const manualLng = document.getElementById('manualLng').value;
            
            if (!manualLat || isNaN(manualLat) || manualLat < -90 || manualLat > 90) {
                showError('location', 'Please enter a valid latitude (-90 to 90)');
                return;
            }
            
            if (!manualLng || isNaN(manualLng) || manualLng < -180 || manualLng > 180) {
                showError('location', 'Please enter a valid longitude (-180 to 180)');
                return;
            }
            
            const lat = parseFloat(manualLat).toFixed(7);
            const lng = parseFloat(manualLng).toFixed(7);
            
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
            document.getElementById('latDisplay').textContent = lat;
            document.getElementById('lngDisplay').textContent = lng;
            document.getElementById('coordsDisplay').classList.add('show');
            
            const btnLocation = document.getElementById('btnUseLocation');
            btnLocation.innerHTML = '✅ Location Set';
            btnLocation.classList.remove('btn-secondary');
            btnLocation.classList.add('btn-success');
            
            showSuccess('location', 'Location set successfully');
            checkFormValidity();
        });
        
        // Form submission
        document.getElementById('sellerApplyForm').addEventListener('submit', function(e) {
            const errors = [];
            
            if (!otpVerified) {
                errors.push('Please verify your mobile number with OTP');
                showError('owner_mobile', 'Mobile number must be verified');
            }
            
            const storeName = document.getElementById('store_name').value.trim();
            if (storeName.length < 3) {
                errors.push('Store name must be at least 3 characters');
                showError('store_name', 'Store name must be at least 3 characters');
            }
            
            const storeType = document.getElementById('store_type').value;
            if (!storeType) {
                errors.push('Please select a store category');
                showError('store_type', 'Please select a store category');
            }
            
            const lat = document.getElementById('lat').value;
            const lng = document.getElementById('lng').value;
            if (!lat || !lng) {
                errors.push('Please set your store location');
                showError('location', 'Please set your store location');
            }
            
            const storeAddress = document.getElementById('store_address').value.trim();
            if (storeAddress.length < 10) {
                errors.push('Please enter a complete store address');
                showError('store_address', 'Please enter a complete address');
            }
            
            const minBillAmount = parseFloat(document.getElementById('min_bill_amount').value);
            if (isNaN(minBillAmount) || minBillAmount < 0) {
                errors.push('Please enter a valid minimum bill amount');
                showError('min_bill_amount', 'Please enter a valid amount');
            }
            
            if (errors.length > 0) {
                e.preventDefault();
                const firstError = document.querySelector('.form-control.error');
                if (firstError) firstError.focus();
                return false;
            }
            
            const btnSubmit = document.getElementById('btnSubmit');
            btnSubmit.classList.add('loading');
            btnSubmit.disabled = true;
        });
        
        // Google Maps Autocomplete
        function initAutocomplete() {
            const input = document.getElementById('locationSearch');
            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                const autocomplete = new google.maps.places.Autocomplete(input, {
                    types: ['establishment', 'geocode'],
                    componentRestrictions: { country: 'in' }
                });
                
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (place.geometry) {
                        const lat = place.geometry.location.lat().toFixed(7);
                        const lng = place.geometry.location.lng().toFixed(7);
                        
                        document.getElementById('lat').value = lat;
                        document.getElementById('lng').value = lng;
                        document.getElementById('latDisplay').textContent = lat;
                        document.getElementById('lngDisplay').textContent = lng;
                        document.getElementById('coordsDisplay').classList.add('show');
                        
                        if (place.formatted_address) {
                            document.getElementById('store_address').value = place.formatted_address;
                        }
                        
                        const btnLocation = document.getElementById('btnUseLocation');
                        btnLocation.innerHTML = '✅ Location Set';
                        btnLocation.classList.remove('btn-secondary');
                        btnLocation.classList.add('btn-success');
                        
                        showSuccess('location', 'Location set from search');
                        checkFormValidity();
                    }
                });
            }
        }
        
        @if(config('services.google.maps_key'))
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config("services.google.maps_key") }}&libraries=places&callback=initAutocomplete';
        script.async = true;
        script.onerror = function() {
            initAutocomplete();
        };
        document.head.appendChild(script);
        @else
        initAutocomplete();
        @endif
        
        // Initialize
        window.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('lat').value && document.getElementById('lng').value) {
                document.getElementById('coordsDisplay').classList.add('show');
                document.getElementById('latDisplay').textContent = document.getElementById('lat').value;
                document.getElementById('lngDisplay').textContent = document.getElementById('lng').value;
            }
            checkFormValidity();
        });
    </script>
</body>
</html>
