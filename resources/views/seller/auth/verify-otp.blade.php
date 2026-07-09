@extends('layouts.seller.guest')

@section('title', __('labels.verify_otp'))
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

                        <h2 class="h2 text-center mb-4">{{ __('labels.verify_otp') }}</h2>
                        <p class="text-muted text-center mb-4">
                            {{ __('labels.otp_sent_to') }} <strong>{{ session('otp_email') }}</strong>
                        </p>

                        <form id="verify-otp-form" action="{{route('seller.register.verify-otp.post')}}" method="post">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label required">{{ __('labels.enter_otp') }}</label>
                                <input type="text" class="form-control @error('otp') is-invalid @enderror"
                                       name="otp" id="otp" placeholder="000000"
                                       maxlength="6" required/>
                                @error('otp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <a href="{{ route('seller.register.resend-otp') }}" class="text-decoration-none">
                                    {{ __('labels.resend_otp') }}
                                </a>
                            </div>

                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary w-100">{{ __('labels.verify') }}</button>
                            </div>
                        </form>

                        <div class="text-center text-secondary mt-3">
                            <a href="{{ route('seller.register') }}">{{ __('labels.back_to_register') }}</a>
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
        const otpInput = document.getElementById('otp');

        // Auto-submit when 6 digits entered
        otpInput.addEventListener('input', function() {
            if (this.value.length === 6) {
                document.getElementById('verify-otp-form').submit();
            }
        });

        // Only allow numbers
        otpInput.addEventListener('keypress', function(e) {
            const keyCode = e.keyCode || e.which;
            const keyValue = String.fromCharCode(keyCode);
            if (!/^[0-9]+$/.test(keyValue)) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush