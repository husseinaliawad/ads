<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Message::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'ad_id' => ['required', 'exists:ads,id'],
            'receiver_id' => ['required', 'exists:users,id', 'different:sender_id'],
            'body' => ['required', 'string', 'min:1', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sender_id' => $this->user()?->id,
        ]);
    }
}
