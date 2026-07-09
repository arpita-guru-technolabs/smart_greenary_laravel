@extends('layouts.seller.guest')

@section('title', __('labels.registration_success'))
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
                    <div class="card-body text-center">
                        <!-- Success Icon -->
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#2ecc71" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                <path d="M9 12l2 2l4 -4" />
                            </svg>
                        </div>

                        <h2 class="h2 text-center mb-3">{{ __('labels.account_created') }}</h2>
                     <!--   <p class="text-muted mb-4">{{ __('labels.account_created_success_message') }}</p> -->

                        <!-- Verification Alert -->
                        <div class="alert alert-info" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                        <path d="M12 9v4" />
                                        <path d="M12 16h.01" />
                                    </svg>
                                </div>
                                <div class="text-start">
                                    <strong>{{ __('labels.verification_needed') }}</strong><br>
                                    {{ __('labels.verification_needed_message') }}
                                </div>
                            </div>
                        </div>

                        <!-- Back to Login Button -->
                        <a href="{{ route('seller.login') }}" class="btn btn-primary w-100 mt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l14 0" />
                                <path d="M5 12l6 6" />
                                <path d="M5 12l6 -6" />
                            </svg>
                            {{ __('labels.back_to_login') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection