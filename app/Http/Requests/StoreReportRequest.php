<?php

namespace App\Http\Requests;

use App\Models\Report;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>|string>
     */
    public function rules(): array
    {
        return [
            'category' => ['required', 'string', Rule::in(Report::categories())],
            'title' => ['required', 'string', 'min:5', 'max:120'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'postcode' => ['required', 'string', 'max:12', 'regex:/^[A-Z]{1,2}\d[A-Z\d]?\s?\d[A-Z]{2}$/i'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'postcode_lookup_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'postcode_lookup_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'additional_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('postcode')) {
            return;
        }

        $postcode = strtoupper(trim((string) $this->input('postcode')));
        $postcode = preg_replace('/\s+/', ' ', $postcode) ?? $postcode;

        $this->merge([
            'postcode' => $postcode,
        ]);
    }
}
