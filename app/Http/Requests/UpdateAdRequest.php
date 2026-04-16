<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ad = $this->route('ad');

        return $ad ? $this->user()?->can('update', $ad) ?? false : false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:150'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'city' => ['required', 'string', 'max:120'],
            'area' => ['nullable', 'string', 'max:120'],
            'category_id' => ['required', 'exists:categories,id'],
            'condition' => ['required', Rule::in(['new', 'used'])],
            'contact_phone' => ['nullable', 'string', 'max:25'],
            'whatsapp_number' => ['nullable', 'string', 'max:25'],
            'status' => ['nullable', Rule::in(['draft', 'pending'])],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'removed_image_ids' => ['nullable', 'array'],
            'removed_image_ids.*' => ['integer', 'exists:images,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'عنوان الإعلان',
            'description' => 'وصف الإعلان',
            'price' => 'السعر',
            'city' => 'المدينة',
            'area' => 'المنطقة',
            'category_id' => 'التصنيف',
            'condition' => 'حالة المنتج',
            'contact_phone' => 'رقم الاتصال',
            'whatsapp_number' => 'رقم واتساب',
            'images' => 'الصور الجديدة',
            'removed_image_ids' => 'الصور المحذوفة',
        ];
    }
}
