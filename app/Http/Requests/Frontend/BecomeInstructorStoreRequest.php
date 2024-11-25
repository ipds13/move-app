<?php

namespace App\Http\Requests\Frontend;

use App\Rules\CustomRecaptcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
use Modules\InstructorRequest\app\Models\InstructorRequestSetting;

class BecomeInstructorStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $instructorRequestSetting = InstructorRequestSetting::first();

        $rules = [
            'payout_account' => ['required'],
            'g-recaptcha-response' => Cache::get('setting')->recaptcha_status == 'active' ? ['required', new CustomRecaptcha()] : 'nullable',
            'extra_information' => ['nullable', 'max:500'],
            'payout_information' => ['required', 'max:500'],
        ];
        if ($instructorRequestSetting?->need_certificate == 1) {
            $rules['certificate'] = ['required', 'array'];
            $rules['certificate.*'] = ['file', 'max:1024', 'mimes:pdf,jpg,jpeg,png']; // Max size in KB (1MB)
        }
        if ($instructorRequestSetting?->need_identity_scan == 1) {
            $rules['identity_scan'] = ['required', 'max:1024', 'mimes:pdf,jpg,jpeg,png'];
        }

        return $rules;
    }

    function messages(): array
    {
        return [
            'payout_account.required' => __('Payout account is required'),
            'payout_account.in' => __('Payout account is invalid'),
            'certificate.required' => __('Certificate is required'),
            'identity_scan.required' => __('Identity scan is required'),
            'certificate.array' => __('Certificate data must be an array'),
            'certificate.*.file' => __('Each certificate must be a valid file'),
            'certificate.*.max' => __('Each certificate must not exceed 1MB in size'),
            'certificate.*.mimes' => __('Each certificate must be a PDF, JPG, JPEG, or PNG file'),
            'identity_scan.max' => __('Certificate size is too large'),
            'identity_scan.mimes' => __('Certificate must be a PDF, DOCX, DOC, JPG, JPEG or PNG file'),
        ];
    }
}
