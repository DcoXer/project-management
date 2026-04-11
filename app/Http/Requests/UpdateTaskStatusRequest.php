<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateStatus', $this->route('task'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:in_progress,review'],
            'proof'  => ['nullable', 'string', 'max:2000', 'required_if:status,review'],
        ];
    }

    public function messages(): array
    {
        return [
            'proof.required_if' => 'Bukti pekerjaan wajib dilampirkan saat submit ke Review.',
        ];
    }
}
