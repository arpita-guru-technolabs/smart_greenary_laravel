{{-- @extends('layouts.seller.guest')

@section('title', 'Address Details - Step 3')
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
                                    <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px; font-weight: 600;">✓</span>
                                    <span class="fw-bold text-success" style="font-size: 13px;">Documents</span>
                                </div>
                            </div>
                            <div class="col-auto px-1">
                                <div style="width: 30px; height: 2px; background: #28a745;"></div>
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <span class="badge rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px; font-weight: 600;">3</span>
                                    <span class="fw-bold text-primary" style="font-size: 13px;">Address</span>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;"></div>
                        </div>
                    </div>

                    <h2 class="h2 text-center mb-4">Address Details</h2>
                    
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

                    <!-- Auto Fetch & From Map Buttons -->
                    <div class="mb-4">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary flex-fill" id="autoFetchBtn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 12l5 -4l5 6l4 -4l5 4" />
                                    <path d="M3 12v6a2 2 0 0 0 2 2h14a2 2 0 0 0 2 -2v-6" />
                                </svg>
                                Auto Fetch
                            </button>
                            <button type="button" class="btn btn-outline-secondary flex-fill" id="fromMapBtn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                    <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                                </svg>
                                From Map
                            </button>
                        </div>
                    </div>

                    <!-- ✅ FIXED: Added seller. to route name -->
                    <form action="{{route('seller.register.save.address')}}" method="post" novalidate>
                        @csrf
                        
                        <!-- Address Field -->
                        <div class="mb-3">
                            <label class="form-label required">Address</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                name="address" id="address" placeholder="123, Shree Complex, Station Road" 
                                value="{{ old('address', session('seller_registration.address_data.address', $seller->address ?? '')) }}" required/>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- City Field -->
                        <div class="mb-3">
                            <label class="form-label required">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                name="city" id="city" placeholder="e.g. Bhuj" 
                                value="{{ old('city', session('seller_registration.address_data.city', $seller->city ?? '')) }}" required/>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Landmark Field -->
                        <div class="mb-3">
                            <label class="form-label required">Landmark</label>
                            <input type="text" class="form-control @error('landmark') is-invalid @enderror" 
                                name="landmark" id="landmark" placeholder="e.g. Near Bus Stand" 
                                value="{{ old('landmark', session('seller_registration.address_data.landmark', $seller->landmark ?? '')) }}" required/>
                            @error('landmark')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- State Field -->
                        <div class="mb-3">
                            <label class="form-label required">State</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                name="state" id="state" placeholder="e.g. Gujarat" 
                                value="{{ old('state', session('seller_registration.address_data.state', $seller->state ?? '')) }}" required/>
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Zipcode Field -->
                        <div class="mb-3">
                            <label class="form-label required">Zip Code</label>
                            <input type="text" class="form-control @error('zipcode') is-invalid @enderror" 
                                name="zipcode" id="zipcode" placeholder="e.g. 370001" 
                                value="{{ old('zipcode', session('seller_registration.address_data.zipcode', $seller->zipcode ?? '')) }}" required/>
                            @error('zipcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Country Field -->
                        <div class="mb-3">
                            <label class="form-label required">Country</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                name="country" id="country" placeholder="e.g. India" 
                                value="{{ old('country', session('seller_registration.address_data.country', $seller->country ?? '')) }}" required/>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Latitude & Longitude -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Latitude</label>
                                    <input type="text" class="form-control @error('latitude') is-invalid @enderror" 
                                        name="latitude" id="latitude" placeholder="e.g. 23.241999" 
                                        value="{{ old('latitude', session('seller_registration.address_data.latitude', $seller->latitude ?? '')) }}" readonly/>
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Longitude</label>
                                    <input type="text" class="form-control @error('longitude') is-invalid @enderror" 
                                        name="longitude" id="longitude" placeholder="e.g. 69.666881" 
                                        value="{{ old('longitude', session('seller_registration.address_data.longitude', $seller->longitude ?? '')) }}" readonly/>
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <!-- ✅ FIXED: Added seller. to route name -->
                            <a href="{{ route('seller.register.documents') }}" class="btn btn-secondary" id="previousBtn">← Previous</a>
                            <button type="submit" class="btn btn-success">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const autoFetchBtn = document.getElementById('autoFetchBtn');
    const fromMapBtn = document.getElementById('fromMapBtn');
    const previousBtn = document.getElementById('previousBtn');
    
    const addressInput = document.getElementById('address');
    const cityInput = document.getElementById('city');
    const landmarkInput = document.getElementById('landmark');
    const stateInput = document.getElementById('state');
    const zipcodeInput = document.getElementById('zipcode');
    const countryInput = document.getElementById('country');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');

    // ============================================
    // SAVE ADDRESS TO SESSION BEFORE NAVIGATING BACK
    // ============================================
    previousBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const addressData = {
            address: addressInput.value,
            city: cityInput.value,
            landmark: landmarkInput.value,
            state: stateInput.value,
            zipcode: zipcodeInput.value,
            country: countryInput.value,
            latitude: latitudeInput.value,
            longitude: longitudeInput.value
        };
        
        // ✅ FIXED: Added seller. to route name
        fetch('{{ route("seller.register.save.address.session") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(addressData)
        })
        .then(response => response.json())
        .then(data => {
            window.location.href = '{{ route("seller.register.documents") }}';
        })
        .catch(error => {
            console.error('Error saving address:', error);
            window.location.href = '{{ route("seller.register.documents") }}';
        });
    });

    // ============================================
    // AUTO FETCH
    // ============================================
    autoFetchBtn.addEventListener('click', function() {
        if (navigator.geolocation) {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Fetching...';
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    latitudeInput.value = lat;
                    longitudeInput.value = lng;
                    
                    fetchAddressFromCoords(lat, lng);
                    
                    autoFetchBtn.disabled = false;
                    autoFetchBtn.innerHTML = '✅ Location Fetched';
                    autoFetchBtn.className = 'btn btn-success flex-fill';
                },
                function(error) {
                    alert('Unable to get location. Please enter address manually.');
                    autoFetchBtn.disabled = false;
                    autoFetchBtn.innerHTML = 'Auto Fetch';
                    autoFetchBtn.className = 'btn btn-outline-primary flex-fill';
                },
                { enableHighAccuracy: true }
            );
        } else {
            alert('Geolocation is not supported by your browser.');
        }
    });

    // ============================================
    // FROM MAP
    // ============================================
    fromMapBtn.addEventListener('click', function() {
        alert('Map feature coming soon! Please enter address manually or use Auto Fetch.');
    });

    // ============================================
    // FETCH ADDRESS FROM COORDINATES
    // ============================================
    function fetchAddressFromCoords(lat, lng) {
        const apiKey = '{{ env('GOOGLE_MAPS_API_KEY') }}';
        
        if (apiKey) {
            fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${apiKey}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'OK' && data.results.length > 0) {
                        const result = data.results[0];
                        const addressComponents = result.address_components;
                        
                        let street = '', city = '', state = '', zipcode = '', country = '', landmark = '';
                        
                        addressComponents.forEach(component => {
                            const types = component.types;
                            
                            if (types.includes('street_number')) {
                                street = component.long_name + ' ' + street;
                            }
                            if (types.includes('route')) {
                                street = street + component.long_name;
                            }
                            if (types.includes('locality') || types.includes('sublocality') || types.includes('administrative_area_level_3')) {
                                city = component.long_name;
                            }
                            if (types.includes('administrative_area_level_1')) {
                                state = component.long_name;
                            }
                            if (types.includes('postal_code')) {
                                zipcode = component.long_name;
                            }
                            if (types.includes('country')) {
                                country = component.long_name;
                            }
                            if (types.includes('sublocality_level_1') || types.includes('neighborhood')) {
                                landmark = component.long_name;
                            }
                        });
                        
                        if (!landmark && result.formatted_address) {
                            const parts = result.formatted_address.split(',');
                            if (parts.length > 0) {
                                landmark = parts[0].trim();
                            }
                        }
                        
                        if (!street) {
                            street = result.formatted_address;
                        }
                        
                        addressInput.value = street;
                        cityInput.value = city || '';
                        stateInput.value = state || '';
                        zipcodeInput.value = zipcode || '';
                        countryInput.value = country || '';
                        landmarkInput.value = landmark || '';
                        
                        saveAddressToSession();
                        
                    } else {
                        alert('Could not fetch address details. Please enter manually.');
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                    alert('Error fetching address. Please enter manually.');
                });
        } else {
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        const address = data.address;
                        
                        addressInput.value = data.display_name;
                        cityInput.value = address.city || address.town || address.village || address.county || '';
                        stateInput.value = address.state || '';
                        zipcodeInput.value = address.postcode || '';
                        countryInput.value = address.country || '';
                        
                        if (data.display_name) {
                            const parts = data.display_name.split(',');
                            if (parts.length > 0) {
                                landmarkInput.value = parts[0].trim();
                            }
                        }
                        
                        saveAddressToSession();
                        
                    } else {
                        alert('Could not fetch address details. Please enter manually.');
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                    alert('Error fetching address. Please enter manually.');
                });
        }
    }

    // ============================================
    // SAVE ADDRESS TO SESSION
    // ============================================
    function saveAddressToSession() {
        const addressData = {
            address: addressInput.value,
            city: cityInput.value,
            landmark: landmarkInput.value,
            state: stateInput.value,
            zipcode: zipcodeInput.value,
            country: countryInput.value,
            latitude: latitudeInput.value,
            longitude: longitudeInput.value
        };
        
        // ✅ FIXED: Added seller. to route name
        fetch('{{ route("seller.register.save.address.session") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(addressData)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Address saved to session:', data);
        })
        .catch(error => {
            console.error('Error saving address to session:', error);
        });
    }

    // ============================================
    // AUTO SAVE ON INPUT CHANGE
    // ============================================
    const inputs = [addressInput, cityInput, landmarkInput, stateInput, zipcodeInput, countryInput];
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            saveAddressToSession();
        });
        input.addEventListener('blur', function() {
            saveAddressToSession();
        });
    });
});
</script>
@endpush --}}


@extends('layouts.seller.guest')

@section('title', 'Address Details - Step 3')
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
                                    <span class="badge rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px; font-weight: 600;">✓</span>
                                    <span class="fw-bold text-success" style="font-size: 13px;">Documents</span>
                                </div>
                            </div>
                            <div class="col-auto px-1">
                                <div style="width: 30px; height: 2px; background: #28a745;"></div>
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <span class="badge rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px; font-weight: 600;">3</span>
                                    <span class="fw-bold text-primary" style="font-size: 13px;">Address</span>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;"></div>
                        </div>
                    </div>

                    <h2 class="h2 text-center mb-4">Address Details</h2>
                    
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

                    <!-- Auto Fetch & From Map Buttons -->
                    <div class="mb-4">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary flex-fill" id="autoFetchBtn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 12l5 -4l5 6l4 -4l5 4" />
                                    <path d="M3 12v6a2 2 0 0 0 2 2h14a2 2 0 0 0 2 -2v-6" />
                                </svg>
                                Auto Fetch
                            </button>
                            <button type="button" class="btn btn-outline-secondary flex-fill" id="fromMapBtn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                    <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                                </svg>
                                From Map
                            </button>
                        </div>
                    </div>

                    <form action="{{route('seller.register.save.address')}}" method="post" novalidate>
                        @csrf
                        
                        <!-- Address Field -->
                        <div class="mb-3">
                            <label class="form-label required">Address</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                name="address" id="address" placeholder="123, Shree Complex, Station Road" 
                                value="{{ old('address', session('seller_registration.address_data.address', $seller->address ?? '')) }}" required/>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- City Field -->
                        <div class="mb-3">
                            <label class="form-label required">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                name="city" id="city" placeholder="e.g. Bhuj" 
                                value="{{ old('city', session('seller_registration.address_data.city', $seller->city ?? '')) }}" required/>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Landmark Field -->
                        <div class="mb-3">
                            <label class="form-label required">Landmark</label>
                            <input type="text" class="form-control @error('landmark') is-invalid @enderror" 
                                name="landmark" id="landmark" placeholder="e.g. Near Bus Stand" 
                                value="{{ old('landmark', session('seller_registration.address_data.landmark', $seller->landmark ?? '')) }}" required/>
                            @error('landmark')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- State Field -->
                        <div class="mb-3">
                            <label class="form-label required">State</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                name="state" id="state" placeholder="e.g. Gujarat" 
                                value="{{ old('state', session('seller_registration.address_data.state', $seller->state ?? '')) }}" required/>
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Zipcode Field -->
                        <div class="mb-3">
                            <label class="form-label required">Zip Code</label>
                            <input type="text" class="form-control @error('zipcode') is-invalid @enderror" 
                                name="zipcode" id="zipcode" placeholder="e.g. 370001" 
                                value="{{ old('zipcode', session('seller_registration.address_data.zipcode', $seller->zipcode ?? '')) }}" required/>
                            @error('zipcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Country Field -->
                        <div class="mb-3">
                            <label class="form-label required">Country</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                name="country" id="country" placeholder="e.g. India" 
                                value="{{ old('country', session('seller_registration.address_data.country', $seller->country ?? '')) }}" required/>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Latitude & Longitude -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Latitude</label>
                                    <input type="text" class="form-control @error('latitude') is-invalid @enderror" 
                                        name="latitude" id="latitude" placeholder="e.g. 23.241999" 
                                        value="{{ old('latitude', session('seller_registration.address_data.latitude', $seller->latitude ?? '')) }}" readonly/>
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Longitude</label>
                                    <input type="text" class="form-control @error('longitude') is-invalid @enderror" 
                                        name="longitude" id="longitude" placeholder="e.g. 69.666881" 
                                        value="{{ old('longitude', session('seller_registration.address_data.longitude', $seller->longitude ?? '')) }}" readonly/>
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('seller.register.documents') }}" class="btn btn-secondary" id="previousBtn">← Previous</a>
                            <button type="submit" class="btn btn-success">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MAP MODAL - Pure CSS (No Bootstrap JS needed) -->
<!-- ============================================ -->
<div id="mapModal" class="map-modal" style="display: none;">
    <div class="map-modal-overlay"></div>
    <div class="map-modal-content">
        <div class="map-modal-header">
            <h5 class="map-modal-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                    <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                </svg>
                Select Location
            </h5>
            <button type="button" class="map-modal-close" id="closeMapModal">&times;</button>
        </div>
        <div class="map-modal-body">
            <!-- Search Input -->
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />
                        </svg>
                    </span>
                    <input type="text" class="form-control" id="mapSearchInput" placeholder="Search for a place...">
                </div>
            </div>

            <!-- Map Container -->
            <div id="map" style="width: 100%; height: 400px; border-radius: 8px;"></div>

            <!-- Selected Location Info -->
            <div class="mt-3 p-3 bg-light rounded">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <small class="text-muted">Selected Location</small>
                        <p class="mb-0 fw-bold" id="selectedLocationDisplay">Click on the map to select a location</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-success" id="confirmLocationBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12l5 5l10 -10" />
                            </svg>
                            Confirm Location
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Google Maps API -->
{{-- <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script> --}}

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>

<style>

    /* Fix for Places Autocomplete dropdown */
.pac-container {
    z-index: 10000 !important;
    background: #fff !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15) !important;
    border: 1px solid #e5e7eb !important;
}

.pac-item {
    padding: 8px 16px !important;
    cursor: pointer !important;
}

.pac-item:hover {
    background: #f3f4f6 !important;
}

.pac-item-query {
    font-weight: 600 !important;
}


/* Map Modal Styles */
.map-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: none;
}

.map-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
}

.map-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.map-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.map-modal-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
}

.map-modal-close {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #6b7280;
    padding: 0 8px;
    line-height: 1;
}

.map-modal-close:hover {
    color: #1f2937;
}

.map-modal-body {
    padding: 20px 24px;
    overflow-y: auto;
    max-height: calc(90vh - 80px);
}

.map-modal.open {
    display: block !important;
}
</style>

<script>
// Global variables for map
let map;
let marker;
let geocoder;
let autocomplete;
let selectedLat = null;
let selectedLng = null;
let selectedAddress = '';

// ============================================
// SAVE ADDRESS TO SESSION - GLOBAL FUNCTION
// ============================================
function saveAddressToSession() {
    const addressData = {
        address: document.getElementById('address').value,
        city: document.getElementById('city').value,
        landmark: document.getElementById('landmark').value,
        state: document.getElementById('state').value,
        zipcode: document.getElementById('zipcode').value,
        country: document.getElementById('country').value,
        latitude: document.getElementById('latitude').value,
        longitude: document.getElementById('longitude').value
    };
    
    fetch('{{ route("seller.register.save.address.session") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(addressData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Address saved to session:', data);
    })
    .catch(error => {
        console.error('Error saving address to session:', error);
    });
}

// ============================================
// MAP MODAL - Open/Close Functions
// ============================================
function openMapModal() {
    const modal = document.getElementById('mapModal');
    modal.classList.add('open');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    if (typeof map !== 'undefined' && map) {
        const defaultLocation = { lat: 23.241999, lng: 69.666881 };
        map.setCenter(defaultLocation);
        map.setZoom(14);
        if (marker) marker.setPosition(null);
        document.getElementById('selectedLocationDisplay').innerHTML = 'Click on the map to select a location';
        document.getElementById('confirmLocationBtn').disabled = true;
        document.getElementById('mapSearchInput').value = '';
        selectedLat = null;
        selectedLng = null;
    }
}

function closeMapModal() {
    const modal = document.getElementById('mapModal');
    modal.classList.remove('open');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// ============================================
// INITIALIZE MAP
// ============================================
function initMap() {
    const defaultLocation = { lat: 23.241999, lng: 69.666881 };
    
    map = new google.maps.Map(document.getElementById('map'), {
        center: defaultLocation,
        zoom: 14,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true,
    });
    
    geocoder = new google.maps.Geocoder();
    
    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP,
    });
    
    map.addListener('click', function(event) {
        const lat = event.latLng.lat();
        const lng = event.latLng.lng();
        setLocation(lat, lng);
    });
    
    marker.addListener('dragend', function() {
        const position = marker.getPosition();
        setLocation(position.lat(), position.lng());
    });
    
    const input = document.getElementById('mapSearchInput');
    
    setTimeout(function() {
        if (input && google.maps.places) {
            autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['geocode', 'establishment'],
                componentRestrictions: { country: 'in' }
            });
            
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (place.geometry && place.geometry.location) {
                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();
                    setLocation(lat, lng);
                    map.setCenter({ lat, lng });
                    map.setZoom(16);
                }
            });
            
            console.log('Places Autocomplete initialized successfully');
        } else {
            console.warn('Input or Places API not available');
        }
    }, 500);
}

// ============================================
// SET LOCATION
// ============================================
function setLocation(lat, lng) {
    selectedLat = lat;
    selectedLng = lng;
    
    marker.setPosition({ lat, lng });
    map.setCenter({ lat, lng });
    
    geocoder.geocode({ location: { lat, lng } }, function(results, status) {
        if (status === 'OK' && results.length > 0) {
            selectedAddress = results[0].formatted_address;
            document.getElementById('selectedLocationDisplay').innerHTML = selectedAddress;
            document.getElementById('confirmLocationBtn').disabled = false;
        } else {
            document.getElementById('selectedLocationDisplay').innerHTML = 'Address not found. Click confirm to use coordinates.';
        }
    });
}

// ============================================
// CONFIRM LOCATION
// ============================================
function confirmLocation() {
    if (!selectedLat || !selectedLng) {
        alert('Please select a location on the map first.');
        return;
    }
    
    geocoder.geocode({ location: { lat: selectedLat, lng: selectedLng } }, function(results, status) {
        if (status === 'OK' && results.length > 0) {
            const result = results[0];
            const addressComponents = result.address_components;
            
            let street = '', city = '', state = '', zipcode = '', country = '', landmark = '';
            
            addressComponents.forEach(component => {
                const types = component.types;
                
                if (types.includes('street_number')) {
                    street = component.long_name + ' ' + street;
                }
                if (types.includes('route')) {
                    street = street + component.long_name;
                }
                if (types.includes('locality') || types.includes('sublocality') || types.includes('administrative_area_level_3')) {
                    city = component.long_name;
                }
                if (types.includes('administrative_area_level_1')) {
                    state = component.long_name;
                }
                if (types.includes('postal_code')) {
                    zipcode = component.long_name;
                }
                if (types.includes('country')) {
                    country = component.long_name;
                }
                if (types.includes('sublocality_level_1') || types.includes('neighborhood')) {
                    landmark = component.long_name;
                }
            });
            
            if (!landmark && result.formatted_address) {
                const parts = result.formatted_address.split(',');
                if (parts.length > 0) {
                    landmark = parts[0].trim();
                }
            }
            
            if (!street) {
                street = result.formatted_address;
            }
            
            document.getElementById('address').value = street;
            document.getElementById('city').value = city || '';
            document.getElementById('state').value = state || '';
            document.getElementById('zipcode').value = zipcode || '';
            document.getElementById('country').value = country || '';
            document.getElementById('landmark').value = landmark || '';
            document.getElementById('latitude').value = selectedLat;
            document.getElementById('longitude').value = selectedLng;
            
            saveAddressToSession();
            closeMapModal();
            
            const successDiv = document.createElement('div');
            successDiv.className = 'alert alert-success alert-dismissible mt-3';
            successDiv.innerHTML = `
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </div>
                    <div>Location selected successfully! Address details have been filled.</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            `;
            
            const mapButtons = document.querySelector('.d-flex.gap-2');
            if (mapButtons) {
                mapButtons.parentNode.insertBefore(successDiv, mapButtons.nextSibling);
            }
            
        } else {
            alert('Could not fetch address details. Please try again.');
        }
    });
}

// ============================================
// DOM READY
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const fromMapBtn = document.getElementById('fromMapBtn');
    const confirmBtn = document.getElementById('confirmLocationBtn');
    const closeBtn = document.getElementById('closeMapModal');
    const overlay = document.querySelector('.map-modal-overlay');
    
    if (fromMapBtn) {
        fromMapBtn.addEventListener('click', openMapModal);
    }
    
    if (closeBtn) closeBtn.addEventListener('click', closeMapModal);
    if (overlay) overlay.addEventListener('click', closeMapModal);
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMapModal();
        }
    });
    
    if (confirmBtn) confirmBtn.addEventListener('click', confirmLocation);
    
    // Auto Fetch
    const autoFetchBtn = document.getElementById('autoFetchBtn');
    if (autoFetchBtn) {
        autoFetchBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Fetching...';
                
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                        
                        fetchAddressFromCoords(lat, lng);
                        
                        autoFetchBtn.disabled = false;
                        autoFetchBtn.innerHTML = '✅ Location Fetched';
                        autoFetchBtn.className = 'btn btn-success flex-fill';
                    },
                    function(error) {
                        alert('Unable to get location. Please enter address manually.');
                        autoFetchBtn.disabled = false;
                        autoFetchBtn.innerHTML = 'Auto Fetch';
                        autoFetchBtn.className = 'btn btn-outline-primary flex-fill';
                    },
                    { enableHighAccuracy: true }
                );
            } else {
                alert('Geolocation is not supported by your browser.');
            }
        });
    }

    function fetchAddressFromCoords(lat, lng) {
        const apiKey = '{{ env('GOOGLE_MAPS_API_KEY') }}';
        
        if (apiKey) {
            fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${apiKey}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'OK' && data.results.length > 0) {
                        const result = data.results[0];
                        const addressComponents = result.address_components;
                        
                        let street = '', city = '', state = '', zipcode = '', country = '', landmark = '';
                        
                        addressComponents.forEach(component => {
                            const types = component.types;
                            
                            if (types.includes('street_number')) {
                                street = component.long_name + ' ' + street;
                            }
                            if (types.includes('route')) {
                                street = street + component.long_name;
                            }
                            if (types.includes('locality') || types.includes('sublocality') || types.includes('administrative_area_level_3')) {
                                city = component.long_name;
                            }
                            if (types.includes('administrative_area_level_1')) {
                                state = component.long_name;
                            }
                            if (types.includes('postal_code')) {
                                zipcode = component.long_name;
                            }
                            if (types.includes('country')) {
                                country = component.long_name;
                            }
                            if (types.includes('sublocality_level_1') || types.includes('neighborhood')) {
                                landmark = component.long_name;
                            }
                        });
                        
                        if (!landmark && result.formatted_address) {
                            const parts = result.formatted_address.split(',');
                            if (parts.length > 0) {
                                landmark = parts[0].trim();
                            }
                        }
                        
                        if (!street) {
                            street = result.formatted_address;
                        }
                        
                        document.getElementById('address').value = street;
                        document.getElementById('city').value = city || '';
                        document.getElementById('state').value = state || '';
                        document.getElementById('zipcode').value = zipcode || '';
                        document.getElementById('country').value = country || '';
                        document.getElementById('landmark').value = landmark || '';
                        
                        saveAddressToSession();
                    } else {
                        alert('Could not fetch address details. Please enter manually.');
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                    alert('Error fetching address. Please enter manually.');
                });
        } else {
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        const address = data.address;
                        
                        document.getElementById('address').value = data.display_name;
                        document.getElementById('city').value = address.city || address.town || address.village || address.county || '';
                        document.getElementById('state').value = address.state || '';
                        document.getElementById('zipcode').value = address.postcode || '';
                        document.getElementById('country').value = address.country || '';
                        
                        if (data.display_name) {
                            const parts = data.display_name.split(',');
                            if (parts.length > 0) {
                                document.getElementById('landmark').value = parts[0].trim();
                            }
                        }
                        
                        saveAddressToSession();
                    } else {
                        alert('Could not fetch address details. Please enter manually.');
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                    alert('Error fetching address. Please enter manually.');
                });
        }
    }

    document.getElementById('previousBtn').addEventListener('click', function(e) {
        e.preventDefault();
        saveAddressToSession();
        window.location.href = '{{ route("seller.register.documents") }}';
    });

    const inputs = [
        document.getElementById('address'),
        document.getElementById('city'),
        document.getElementById('landmark'),
        document.getElementById('state'),
        document.getElementById('zipcode'),
        document.getElementById('country')
    ];
    
    inputs.forEach(input => {
        if (input) {
            input.addEventListener('change', saveAddressToSession);
            input.addEventListener('blur', saveAddressToSession);
        }
    });
});
</script>
@endpush