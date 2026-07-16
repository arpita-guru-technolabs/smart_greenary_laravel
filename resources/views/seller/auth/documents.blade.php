{{-- @extends('layouts.seller.guest')

@section('title', 'Upload Documents - Step 2')
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
                    
                    <!-- ============================================ -->
                    <!-- STEP PROGRESS INDICATOR - Like Mobile App -->
                    <!-- ============================================ -->
                    <div class="mb-4">
                        <div class="row g-0 text-center align-items-center">
                            <!-- Step 1 - Complete -->
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px; font-weight: 600;">✓</span>
                                    <span class="fw-bold text-success" style="font-size: 13px;">Personal Info</span>
                                </div>
                            </div>
                            <!-- Line -->
                            <div class="col-auto px-1">
                                <div style="width: 30px; height: 2px; background: #28a745;"></div>
                            </div>
                            <!-- Step 2 - Active -->
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <span class="badge rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px; font-weight: 600;">2</span>
                                    <span class="fw-bold text-primary" style="font-size: 13px;">Documents</span>
                                </div>
                            </div>
                            <!-- Line -->
                            <div class="col-auto px-1">
                                <div style="width: 30px; height: 2px; background: #dee2e6;"></div>
                            </div>
                            <!-- Step 3 - Inactive -->
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <span class="badge rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px; font-weight: 600;">3</span>
                                    <span class="text-secondary" style="font-size: 13px;">Address</span>
                                </div>
                            </div>
                        </div>
                        <!-- Progress Bar -->
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 66%;"></div>
                        </div>
                    </div>

                    <h2 class="h2 text-center mb-4">Upload Documents</h2>
                    
                    <!-- Flash Messages -->
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

                    <form action="{{route('seller.register.upload.documents')}}" method="post" enctype="multipart/form-data" novalidate>
                        @csrf
                        
                      <!-- Business License (Optional) -->
<div class="mb-3">
    <label class="form-label">Business License</label>
    <input type="file" class="form-control @error('business_license') is-invalid @enderror" 
           name="business_license" accept=".jpg,.jpeg,.png,.pdf"/>
    @error('business_license')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <small class="form-hint text-muted">Upload a clear copy of your business license. Accepted formats: JPEG, PNG, PDF. Max size: 2MB.</small>
</div>

<!-- Articles of Incorporation (Optional) -->
<div class="mb-3">
    <label class="form-label">Articles of Incorporation</label>
    <input type="file" class="form-control @error('articles_of_incorporation') is-invalid @enderror" 
           name="articles_of_incorporation" accept=".jpg,.jpeg,.png,.pdf"/>
    @error('articles_of_incorporation')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <small class="form-hint text-muted">Provide your company's articles of incorporation or certificate of incorporation. File must be clear and readable.</small>
</div>

<!-- National Identity Card (Required) -->
<div class="mb-3">
    <label class="form-label required">National Identity Card</label>
    <input type="file" class="form-control @error('national_identity_card') is-invalid @enderror" 
           name="national_identity_card" accept=".jpg,.jpeg,.png,.pdf" required/>
    @error('national_identity_card')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <small class="form-hint text-muted">Upload a government-issued photo ID (passport, driver's license, or national ID card). Both front and back sides if applicable.</small>
</div>

<!-- Authorized Signature (Required) -->
<div class="mb-3">
    <label class="form-label required">Authorized Signature</label>
    <input type="file" class="form-control @error('authorized_signature') is-invalid @enderror" 
           name="authorized_signature" accept=".jpg,.jpeg,.png" required/>
    @error('authorized_signature')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <small class="form-hint text-muted">Upload your authorized signature. Accepted formats: JPEG, PNG. Max size: 2MB.</small>
</div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('seller.register') }}" class="btn btn-secondary">← Previous</a>
                            <button type="submit" class="btn btn-primary">Next →</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}

@extends('layouts.seller.guest')

@section('title', 'Upload Documents - Step 2')
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
                    
                    <!-- Step Progress Indicator -->
                    <div class="mb-4">
                        <div class="row g-0 text-center align-items-center">
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px; font-weight: 600;">✓</span>
                                    <span class="fw-bold text-success" style="font-size: 13px;">Personal Info</span>
                                </div>
                            </div>
                            <div class="col-auto px-1">
                                <div style="width: 30px; height: 2px; background: #28a745;"></div>
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <span class="badge rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px; font-weight: 600;">2</span>
                                    <span class="fw-bold text-primary" style="font-size: 13px;">Documents</span>
                                </div>
                            </div>
                            <div class="col-auto px-1">
                                <div style="width: 30px; height: 2px; background: #dee2e6;"></div>
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <span class="badge rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px; font-weight: 600;">3</span>
                                    <span class="text-secondary" style="font-size: 13px;">Address</span>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 66%;"></div>
                        </div>
                    </div>

                    <h2 class="h2 text-center mb-4">Upload Documents</h2>
                    
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

                    <form action="{{route('seller.register.upload.documents')}}" method="post" enctype="multipart/form-data" novalidate>
                        @csrf
                        
                        <!-- Business License -->
                        <div class="mb-3">
                            <label class="form-label {{ (isset($documents['business_license']) && $documents['business_license']['exists']) ? '' : 'required' }}">Business License</label>
                            <input type="file" class="form-control @error('business_license') is-invalid @enderror" 
                                name="business_license" accept=".jpg,.jpeg,.png,.pdf" 
                                {{ (isset($documents['business_license']) && $documents['business_license']['exists']) ? '' : 'required' }}/>
                            @if(isset($documents['business_license']) && $documents['business_license']['exists'])
                                <div class="mt-2 text-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10" />
                                    </svg>
                                    Already uploaded: <strong>{{ $documents['business_license']['name'] }}</strong> 
                                    ({{ number_format($documents['business_license']['size'] / 1024, 2) }} KB)
                                </div>
                            @endif
                            @error('business_license')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-hint text-muted">Upload a clear copy of your business license. Accepted formats: JPEG, PNG, PDF. Max size: 2MB.</small>
                        </div>

                        <!-- Articles of Incorporation -->
                        <div class="mb-3">
                            <label class="form-label {{ (isset($documents['articles_of_incorporation']) && $documents['articles_of_incorporation']['exists']) ? '' : 'required' }}">Articles of Incorporation</label>
                            <input type="file" class="form-control @error('articles_of_incorporation') is-invalid @enderror" 
                                name="articles_of_incorporation" accept=".jpg,.jpeg,.png,.pdf" 
                                {{ (isset($documents['articles_of_incorporation']) && $documents['articles_of_incorporation']['exists']) ? '' : 'required' }}/>
                            @if(isset($documents['articles_of_incorporation']) && $documents['articles_of_incorporation']['exists'])
                                <div class="mt-2 text-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10" />
                                    </svg>
                                    Already uploaded: <strong>{{ $documents['articles_of_incorporation']['name'] }}</strong> 
                                    ({{ number_format($documents['articles_of_incorporation']['size'] / 1024, 2) }} KB)
                                </div>
                            @endif
                            @error('articles_of_incorporation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-hint text-muted">Provide your company's articles of incorporation or certificate of incorporation. File must be clear and readable.</small>
                        </div>

                        <!-- National Identity Card (Required) -->
                        <div class="mb-3">
                            <label class="form-label required">National Identity Card</label>
                            <input type="file" class="form-control @error('national_identity_card') is-invalid @enderror" 
                                   name="national_identity_card" accept=".jpg,.jpeg,.png,.pdf" 
                                   {{ (isset($documents['national_identity_card']) && $documents['national_identity_card']['exists']) ? '' : 'required' }}/>
                            @if(isset($documents['national_identity_card']) && $documents['national_identity_card']['exists'])
                                <div class="mt-2 text-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10" />
                                    </svg>
                                    Already uploaded: <strong>{{ $documents['national_identity_card']['name'] }}</strong> 
                                    ({{ number_format($documents['national_identity_card']['size'] / 1024, 2) }} KB)
                                </div>
                            @endif
                            @error('national_identity_card')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-hint text-muted">Upload a government-issued photo ID (passport, driver's license, or national ID card). Both front and back sides if applicable.</small>
                        </div>

                        <!-- Authorized Signature (Required) -->
                        <div class="mb-3">
                            <label class="form-label required">Authorized Signature</label>
                            <input type="file" class="form-control @error('authorized_signature') is-invalid @enderror" 
                                   name="authorized_signature" accept=".jpg,.jpeg,.png" 
                                   {{ (isset($documents['authorized_signature']) && $documents['authorized_signature']['exists']) ? '' : 'required' }}/>
                            @if(isset($documents['authorized_signature']) && $documents['authorized_signature']['exists'])
                                <div class="mt-2 text-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l5 5l10 -10" />
                                    </svg>
                                    Already uploaded: <strong>{{ $documents['authorized_signature']['name'] }}</strong> 
                                    ({{ number_format($documents['authorized_signature']['size'] / 1024, 2) }} KB)
                                </div>
                            @endif
                            @error('authorized_signature')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-hint text-muted">Upload your authorized signature. Accepted formats: JPEG, PNG. Max size: 2MB.</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('seller.register') }}" class="btn btn-secondary">← Previous</a>
                            <button type="submit" class="btn btn-primary">Next →</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection