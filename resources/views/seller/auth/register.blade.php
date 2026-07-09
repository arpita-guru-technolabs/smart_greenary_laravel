@extends('layouts.seller.guest')

@section('title', __('labels.create_seller_account'))
@section('content')
    <div>
        <div class="page page-center">
            <div class="container container-tight py-4">
                <div class="text-center mb-4">
                    <a href="." class="navbar-brand navbar-brand-autodark">
                        @if(($systemSettings['demoMode'] ?? false))
                            <img src="{{asset('logos/hyper-local-logo.png')}}" alt="{{$systemSettings['appName'] ?? ""}}" width="150px">
                        @else
                            <img src="{{!empty($systemSettings['logo'])?$systemSettings['logo'] : asset('logos/hyper-local-logo.png')}}" alt="{{$systemSettings['appName'] ?? ""}}" width="150px">
                        @endif
                    </a>
                </div>
                <div class="card card-md">
                    <div class="card-body">
                        {{-- Success/Error Flash Messages Only --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                <div class="d-flex">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l5 5l10 -10" />
                                        </svg>
                                    </div>
                                    <div>{{ session('success') }}</div>
                                </div>
                                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <div class="d-flex">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 9v4" />
                                            <path d="M12 16h.01" />
                                        </svg>
                                    </div>
                                    <div>{{ session('error') }}</div>
                                </div>
                                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                            </div>
                        @endif
                        
                        <h2 class="h2 text-center mb-4">{{ __('labels.create_seller_account') }}</h2>
                        
                        <form id="register-form" action="{{route('seller.register.post')}}" method="post" autocomplete="off" novalidate>
                            @csrf
                            
                            <!-- Name Field -->
                            <div class="mb-3">
                                <label class="form-label required">{{ __('labels.full_name') }}</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" id="name" placeholder="{{ __('labels.enter_full_name') }}" 
                                       value="{{ old('name') }}" required/>
                                <div class="invalid-feedback" id="name-error">
                                    @error('name'){{ $message }}@enderror
                                </div>
                            </div>
                            
                            <!-- Email Field -->
                            <div class="mb-3">
                                <label class="form-label required">{{ __('labels.email_address') }}</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" id="email" placeholder="your@email.com" 
                                       value="{{ old('email') }}" required/>
                                <div class="invalid-feedback" id="email-error">
                                    @error('email'){{ $message }}@enderror
                                </div>
                            </div>
                            
                            <!-- Phone Field -->
                            <div class="mb-3">
                                <label class="form-label required">{{ __('labels.phone_number') }}</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" id="phone" placeholder="{{ __('labels.enter_phone_number') }}" 
                                       value="{{ old('phone') }}" required/>
                                <div class="invalid-feedback" id="phone-error">
                                    @error('phone'){{ $message }}@enderror
                                </div>
                            </div>
                            
                            <!-- Password Field -->
                            <div class="mb-3">
                                <label class="form-label required">{{ __('labels.password') }}</label>
                                <div class="input-group input-group-flat">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           name="password" id="password" placeholder="{{ __('labels.enter_password') }}" 
                                           required/>
                                    <span class="input-group-text">
                                        <a href="#" class="link-secondary" title="Show password" id="password-toggle" data-bs-toggle="tooltip">Show</a>
                                    </span>
                                </div>
                                <div class="invalid-feedback" id="password-error" style="display: block;">
                                    @error('password'){{ $message }}@enderror
                                </div>
                                <small class="form-hint text-muted">
                                    Password must be at least 8 characters, including uppercase, lowercase, number, and special character (@, $, !, %, *, ?, &)
                                </small>
                            </div>
                            
                            <!-- Confirm Password Field -->
                            <div class="mb-3">
                                <label class="form-label required">{{ __('labels.confirm_password') }}</label>
                                <div class="input-group input-group-flat">
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           name="password_confirmation" id="password_confirmation" 
                                           placeholder="{{ __('labels.confirm_password') }}" required/>
                                </div>
                                <div class="invalid-feedback" id="password_confirmation-error" style="display: block;">
                                    @error('password_confirmation'){{ $message }}@enderror
                                </div>
                            </div>
                            
                            <!-- Terms Field -->
                            <div class="mb-3">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" 
                                           name="terms" id="terms" required/>
                                    <span class="form-check-label">{{ __('labels.accept_terms') }}</span>
                                </label>
                                <div class="invalid-feedback" id="terms-error">
                                    @error('terms'){{ $message }}@enderror
                                </div>
                            </div>
                            
                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary w-100" id="submit-btn">{{ __('labels.register') }}</button>
                            </div>
                        </form>
                        
                        <div class="text-center text-secondary mt-3">
                            {{ __('labels.already_have_account') }} <a href="{{ route('seller.login') }}">{{ __('labels.sign_in') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle
        document.getElementById('password-toggle')?.addEventListener('click', function(e) {
            e.preventDefault();
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.textContent = 'Hide';
            } else {
                passwordInput.type = 'password';
                this.textContent = 'Show';
            }
        });

        // Get form elements
        const form = document.getElementById('register-form');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const termsInput = document.getElementById('terms');

        // Real-time validation on input
        nameInput.addEventListener('input', function() { validateField(this); });
        emailInput.addEventListener('input', function() { validateField(this); });
        phoneInput.addEventListener('input', function() { validateField(this); });
        
        passwordInput.addEventListener('input', function() { 
            validateField(this);
            if (confirmPasswordInput.value) {
                validateField(confirmPasswordInput);
            }
        });
        
        confirmPasswordInput.addEventListener('input', function() { 
            validateField(this);
        });
        
        termsInput.addEventListener('change', function() { validateField(this); });

        // Form submit
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const allInputs = [nameInput, emailInput, phoneInput, passwordInput, confirmPasswordInput, termsInput];
            
            allInputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                e.preventDefault();
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.focus();
                }
            }
        });

        // Validate single field
        function validateField(input) {
            const name = input.name;
            const value = input.value.trim();
            const errorElement = document.getElementById(name + '-error');
            const formControl = input;

            let isValid = true;
            let errorMessage = '';

            // Remove existing error
            formControl.classList.remove('is-invalid');
            if (errorElement) {
                errorElement.textContent = '';
                errorElement.style.display = 'none';
            }

            // Validation rules
            switch(name) {
                case 'name':
                    if (!value || value.length < 2) {
                        isValid = false;
                        errorMessage = 'Name must be at least 2 characters.';
                    }
                    break;

                case 'email':
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Email is required.';
                    } else if (!emailRegex.test(value)) {
                        isValid = false;
                        errorMessage = 'Please enter a valid email address.';
                    }
                    break;

                case 'phone':
                    const phoneRegex = /^[0-9+\-\s()]{7,20}$/;
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Phone number is required.';
                    } else if (!phoneRegex.test(value)) {
                        isValid = false;
                        errorMessage = 'Please enter a valid phone number.';
                    }
                    break;

                case 'password':
                    const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                    
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Password is required.';
                    } else if (value.length < 8) {
                        isValid = false;
                        errorMessage = 'Password must be at least 8 characters.';
                    } else if (!strongPasswordRegex.test(value)) {
                        isValid = false;
                        errorMessage = 'Password must include uppercase, lowercase, number, and special character.';
                    }
                    
                    // Also validate confirm password if it has value
                    if (confirmPasswordInput.value) {
                        validateField(confirmPasswordInput);
                    }
                    break;

                case 'password_confirmation':
                    const passwordValue = document.getElementById('password').value;
                    if (!value) {
                        isValid = false;
                        errorMessage = 'Please confirm your password.';
                    } else if (!passwordValue) {
                        isValid = false;
                        errorMessage = 'Please enter password first.';
                    } else if (value !== passwordValue) {
                        isValid = false;
                        errorMessage = 'Passwords do not match.';
                    }
                    break;

                case 'terms':
                    if (!input.checked) {
                        isValid = false;
                        errorMessage = 'You must accept the terms and conditions.';
                    }
                    break;
            }

            // Show/hide error
            if (!isValid) {
                formControl.classList.add('is-invalid');
                if (errorElement) {
                    errorElement.textContent = errorMessage;
                    errorElement.style.display = 'block';
                }
            } else {
                formControl.classList.remove('is-invalid');
                if (errorElement) {
                    errorElement.textContent = '';
                    errorElement.style.display = 'none';
                }
            }

            return isValid;
        }

    });
</script>
@endpush