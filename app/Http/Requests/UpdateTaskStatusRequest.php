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
            'status'     => ['required', 'in:in_progress,review'],
            'proof'      => ['nullable', 'string', 'max:2000'],
            'proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf,zip', 'max:10240'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('status') === 'review'
                && ! $this->filled('proof')
                && ! $this->hasFile('proof_file')
            ) {
                $validator->errors()->add('proof', 'Bukti pekerjaan wajib dilampirkan saat submit ke Review.');
            }
        });
    }
}
