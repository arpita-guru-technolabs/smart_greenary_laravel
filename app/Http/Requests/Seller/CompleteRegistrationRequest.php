<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class CompleteRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // User ID
            'user_id' => 'required|exists:users,id',
            
            // Address Fields (Required)
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'landmark' => 'required|string|max:200',
            'zipcode' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            
            // Document Fields (Required)
            'business_license' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'articles_of_incorporation' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'national_identity_card' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'authorized_signature' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'Invalid user ID',
            
            'address.required' => 'Address is required',
            'city.required' => 'City is required',
            'state.required' => 'State is required',
            'landmark.required' => 'Landmark is required',
            'zipcode.required' => 'Zip code is required',
            'country.required' => 'Country is required',
            
            'business_license.required' => 'Business License is required',
            'business_license.file' => 'Business License must be a file',
            'business_license.mimes' => 'Business License must be a JPEG, PNG, or PDF',
            'business_license.max' => 'Business License must not exceed 2MB',
            
            'articles_of_incorporation.required' => 'Articles of Incorporation is required',
            'articles_of_incorporation.file' => 'Articles of Incorporation must be a file',
            'articles_of_incorporation.mimes' => 'Articles of Incorporation must be a JPEG, PNG, or PDF',
            'articles_of_incorporation.max' => 'Articles of Incorporation must not exceed 2MB',
            
            'national_identity_card.required' => 'National Identity Card is required',
            'national_identity_card.file' => 'National Identity Card must be a file',
            'national_identity_card.mimes' => 'National Identity Card must be a JPEG, PNG, or PDF',
            'national_identity_card.max' => 'National Identity Card must not exceed 2MB',
            
            'authorized_signature.required' => 'Authorized Signature is required',
            'authorized_signature.file' => 'Authorized Signature must be a file',
            'authorized_signature.mimes' => 'Authorized Signature must be a JPEG or PNG',
            'authorized_signature.max' => 'Authorized Signature must not exceed 2MB',
        ];
    }
}