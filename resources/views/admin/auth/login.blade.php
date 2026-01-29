@include('admin.header')

<style>
/* Professional Admin Login Page Styles - Kutoot Brand */
:root {
    --kutoot-primary: #B22234;
    --kutoot-secondary: #FF8C00;
    --color-text-primary: #1a1a1a;
    --color-text-secondary: #666666;
    --color-border: #e8e8e8;
    --color-bg-input: #fafafa;
    --color-white: #ffffff;
    --spacing-md: 20px;
    --spacing-lg: 30px;
    --spacing-xl: 35px;
    --border-radius-sm: 12px;
    --border-radius-md: 20px;
    --border-radius-lg: 24px;
    --transition-normal: 0.3s ease;
}

.admin-login-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--kutoot-primary) 0%, var(--kutoot-secondary) 100%);
    position: relative;
    overflow: hidden;
    padding: var(--spacing-md);
}

.admin-login-wrapper::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 15s ease-in-out infinite;
}

.admin-login-wrapper::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 300px;
    background: linear-gradient(to top, rgba(0,0,0,0.1), transparent);
    pointer-events: none;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.login-container {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 440px;
    margin: 0 auto;
}

/* Brand logo section */
.login-brand-box {
    text-align: center;
    margin-bottom: var(--spacing-xl);
    animation: fadeInDown 0.6s ease;
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.login-brand-box .logo-container {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-md) var(--spacing-lg);
    background: var(--color-white);
    border-radius: var(--border-radius-md);
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    transition: transform var(--transition-normal);
}

.login-brand-box .logo-container:hover {
    transform: translateY(-2px);
}

.login-brand-box .logo-img {
    max-width: 200px;
    max-height: 70px;
    object-fit: contain;
    display: block;
}

.login-brand-box .logo-fallback {
    display: none;
    font-size: 32px;
    font-weight: 800;
    color: var(--color-white);
    letter-spacing: 3px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

/* Main login card */
.login-card {
    background: var(--color-white);
    border-radius: var(--border-radius-lg);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
    border: none;
    animation: fadeInUp 0.6s ease 0.2s both;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.login-card .card-header {
    background: transparent;
    border-bottom: 1px solid var(--color-border);
    padding: 32px var(--spacing-xl) 28px;
    text-align: center;
}

.login-card .card-header h4 {
    margin: 0 0 12px 0;
    font-size: 28px;
    font-weight: 700;
    color: var(--color-text-primary);
    line-height: 1.2;
}

.login-card .card-header p {
    margin: 0;
    color: var(--color-text-secondary);
    font-size: 15px;
    line-height: 1.6;
}

.login-card .card-body {
    padding: var(--spacing-lg) var(--spacing-xl) var(--spacing-xl);
}

/* Form elements */
.login-card .form-group {
    margin-bottom: 24px;
}

.login-card .form-group label {
    font-weight: 600;
    color: var(--color-text-secondary);
    margin-bottom: 10px;
    font-size: 14px;
    letter-spacing: 0.3px;
    display: block;
}

/* Input Group with Prepend Icon */
.input-group {
    display: flex;
    align-items: stretch;
    width: 100%;
}

.input-group-prepend {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    min-width: 52px;
    background-color: var(--color-bg-input);
    border: 2px solid var(--color-border);
    border-right: none;
    border-radius: var(--border-radius-sm) 0 0 var(--border-radius-sm);
    transition: all var(--transition-normal);
}

.input-group-prepend .input-icon {
    color: #aaa;
    font-size: 16px;
    transition: color var(--transition-normal);
}

/* Icon color change on focus */
.input-group:focus-within .input-group-prepend {
    border-color: var(--kutoot-primary);
    background-color: rgba(178, 34, 52, 0.05);
}

.input-group:focus-within .input-group-prepend .input-icon {
    color: var(--kutoot-primary);
}

/* Input field styling */
.login-card .form-control {
    height: 52px;
    border-radius: 0 var(--border-radius-sm) var(--border-radius-sm) 0;
    border: 2px solid var(--color-border);
    border-left: none;
    padding: 14px 50px 14px 16px;
    font-size: 15px;
    line-height: 1.5;
    transition: all var(--transition-normal);
    background-color: var(--color-bg-input);
    color: var(--color-text-primary);
    flex: 1;
    min-width: 0;
}

.login-card .form-control::placeholder {
    color: #bbb;
    font-size: 14px;
}

.input-group:hover .input-group-prepend {
    border-color: #d0d0d0;
}

.input-group:hover .form-control {
    border-color: #d0d0d0;
}

.login-card .form-control:focus {
    border-color: var(--kutoot-primary);
    background-color: var(--color-white);
    box-shadow: 0 0 0 4px rgba(178, 34, 52, 0.1);
    outline: none;
}

/* Error state for input group */
.input-group.is-invalid .input-group-prepend {
    border-color: #dc3545;
}

.input-group.is-invalid .form-control {
    border-color: #dc3545;
}

/* Error state */
.login-card .form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 13px;
    margin-top: 6px;
    font-weight: 500;
}

/* Password field wrapper for toggle button */
.password-wrapper {
    position: relative;
    flex: 1;
    min-width: 0;
    display: flex;
}

.password-wrapper .form-control {
    padding-right: 50px;
}

/* Password visibility toggle */
.password-toggle {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #aaa;
    cursor: pointer;
    padding: 8px;
    z-index: 10;
    transition: color var(--transition-normal);
    font-size: 16px;
}

.password-toggle:hover,
.password-toggle:focus {
    color: var(--kutoot-primary);
    outline: 2px solid var(--kutoot-primary);
    outline-offset: 2px;
    border-radius: 4px;
}

/* Checkbox */
.login-card .custom-control-label {
    font-size: 14px;
    color: var(--color-text-secondary);
    cursor: pointer;
    padding-left: 8px;
    user-select: none;
}

.login-card .custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
    background-color: var(--kutoot-primary);
    border-color: var(--kutoot-primary);
}

/* Login button */
.login-card .btn-login {
    height: 52px;
    border-radius: var(--border-radius-sm);
    font-size: 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    background: linear-gradient(135deg, var(--kutoot-primary) 0%, var(--kutoot-secondary) 100%);
    border: none;
    color: var(--color-white);
    box-shadow: 0 10px 25px rgba(178, 34, 52, 0.35);
    transition: all var(--transition-normal);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.login-card .btn-login:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 15px 35px rgba(178, 34, 52, 0.45);
}

.login-card .btn-login:active:not(:disabled) {
    transform: translateY(0);
}

.login-card .btn-login:focus {
    outline: 3px solid rgba(178, 34, 52, 0.5);
    outline-offset: 3px;
}

/* Footer */
.login-footer {
    text-align: center;
    margin-top: var(--spacing-lg);
    color: rgba(255, 255, 255, 0.95);
    font-size: 14px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

/* Decorative elements */
.login-decor {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    pointer-events: none;
}

.login-decor-1 {
    width: 300px;
    height: 300px;
    top: -100px;
    right: -100px;
}

.login-decor-2 {
    width: 200px;
    height: 200px;
    bottom: 50px;
    left: -50px;
}

.login-decor-3 {
    width: 150px;
    height: 150px;
    top: 40%;
    right: 10%;
}

/* Responsive */
@media (max-width: 480px) {
    .login-container {
        padding: 15px;
    }
    .login-card .card-body {
        padding: 25px 20px 30px;
    }
    .login-card .card-header {
        padding: 25px 20px 20px;
    }
    .login-brand-box .logo-container {
        padding: 15px 25px;
    }
    .login-brand-box .logo-img {
        max-width: 160px;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>

<div class="admin-login-wrapper">
    <!-- Decorative circles -->
    <div class="login-decor login-decor-1" aria-hidden="true"></div>
    <div class="login-decor login-decor-2" aria-hidden="true"></div>
    <div class="login-decor login-decor-3" aria-hidden="true"></div>

    <div class="login-container">
        <div class="login-brand-box">
            <div class="logo-container">
                <img src="{{ asset($setting?->logo ?? 'backend/images/logo.png') }}"
                     alt="{{ $setting?->app_name ?? 'Kutoot' }} Logo"
                     class="logo-img"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <span class="logo-fallback">KUTOOT</span>
            </div>
        </div>

        <div class="card login-card">
            <div class="card-header">
                <h4>{{__('admin.Welcome Back')}}</h4>
                <p>{{__('admin.Sign in to your admin account')}}</p>
            </div>

            <div class="card-body">
                <form class="needs-validation" novalidate action="{{ route('admin.login') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="email">{{__('admin.Email')}}</label>
                        <div class="input-group @error('email') is-invalid @enderror">
                            <div class="input-group-prepend">
                                <i class="fas fa-envelope input-icon" aria-hidden="true"></i>
                            </div>
                            <input id="email"
                                   type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   name="email"
                                   placeholder="admin@example.com"
                                   tabindex="1"
                                   autofocus
                                   required
                                   autocomplete="email"
                                   value="{{ old('email') }}">
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">{{__('admin.Password')}}</label>
                        <div class="input-group @error('password') is-invalid @enderror">
                            <div class="input-group-prepend">
                                <i class="fas fa-lock input-icon" aria-hidden="true"></i>
                            </div>
                            <div class="password-wrapper">
                                <input id="password"
                                       type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password"
                                       placeholder="Enter your password"
                                       tabindex="2"
                                       required
                                       autocomplete="current-password">
                                <button type="button"
                                        class="password-toggle"
                                        aria-label="Toggle password visibility"
                                        onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggle-icon" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox"
                                   name="remember"
                                   class="custom-control-input"
                                   tabindex="3"
                                   id="remember"
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="remember">{{__('admin.Remember Me')}}</label>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-login btn-lg btn-block" tabindex="4">
                            <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>
                            {{__('admin.Login')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="login-footer">
            {{ $setting?->copyright ?? '© ' . date('Y') . ' Kutoot. All rights reserved.' }}
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggle-icon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>

@include('admin.footer')


