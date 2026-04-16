<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Report::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'ad_id' => ['required', 'exists:ads,id'],
            'reason' => ['required', 'string', 'max:120'],
            'details' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'ad_id' => 'الإعلان',
            'reason' => 'سبب البلاغ',
            'details' => 'تفاصيل البلاغ',
        ];
    }
}
