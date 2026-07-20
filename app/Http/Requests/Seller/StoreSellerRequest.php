<?php

namespace App\Http\Requests\Seller;

use App\Enums\Seller\SellerVerificationStatusEnum;
use App\Enums\Seller\SellerVisibilityStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Enum;

class StoreSellerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */

    public function rules_BKUP(): array
    {
        $rules = [
            // User fields
            'name' => 'required_without:user_id|string|max:255',
            'email' => 'required_without:user_id|string|email|max:255|unique:users,email',
            'mobile' => 'required_without:user_id|regex:/^([0-9\s\-\+\(\)]*)$/|min:7|unique:users,mobile',
            'password' => 'required_without:user_id|string',
            // Seller fields
            //'user_id' => 'nullable|exists:users,id',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'landmark' => 'required|string|max:255',
            'zipcode' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'business_license' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'articles_of_incorporation' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'national_identity_card' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'authorized_signature' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
        if (!Route::is('seller-api.register')) {
            $rules['verification_status'] = ['required', new Enum(SellerVerificationStatusEnum::class)];
            $rules['visibility_status'] = ['required', new Enum(SellerVisibilityStatusEnum::class)];
        }
        return $rules;
    }

   /* public function rules(): array
    {
        // Base rules always required
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|string|max:20|unique:users,mobile',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|same:password',
        ];

        // Add address & file rules ONLY if NOT register route
        if (!Route::is('seller-api.register')) {
             $rules['user_id'] = 'required|exists:users,id';  
            $rules['address'] = 'required|string|max:255';
            $rules['city'] = 'required|string|max:255';
            $rules['state'] = 'required|string|max:255';
            $rules['landmark'] = 'required|string|max:255';
            $rules['zipcode'] = 'required|string|max:255';
            $rules['country'] = 'required|string|max:255';
            $rules['latitude'] = 'nullable|string|max:255';
            $rules['longitude'] = 'nullable|string|max:255';
            $rules['business_license'] = 'required|image|mimes:jpeg,png,jpg,webp|max:2048';
            $rules['articles_of_incorporation'] = 'required|image|mimes:jpeg,png,jpg,webp|max:2048';
            $rules['national_identity_card'] = 'required|image|mimes:jpeg,png,jpg,webp|max:2048';
            $rules['authorized_signature'] = 'required|image|mimes:jpeg,png,jpg,webp|max:2048';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required_without' => __('validation.required', ['attribute' => 'Name']),
            'email.required_without' => __('validation.required', ['attribute' => 'Email']),
            'mobile.required_without' => __('validation.required', ['attribute' => 'Mobile']),
            'password.required_without' => __('validation.required', ['attribute' => 'Password']),
            'business_license.required' => __('validation.required', ['attribute' => 'Business License']),
            'articles_of_incorporation.required' => __('validation.required', ['attribute' => 'Articles of Incorporation']),
            'national_identity_card.required' => __('validation.required', ['attribute' => 'National Identity Card']),
            'authorized_signature.required' => __('validation.required', ['attribute' => 'Authorized Signature']),
        ];
    }*/

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|string|max:20|unique:users,mobile',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This email is already registered',
            'mobile.required' => 'Mobile number is required',
            'mobile.unique' => 'This mobile number is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
        ];
    }
}
