<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLifestylePrescriptionRequest extends FormRequest
{
    public const CATEGORIES = ['sleep', 'exercise', 'nutrition', 'stress', 'social', 'other'];

    /**
     * Authorization is handled by the route middleware (auth + role:psychiatrist).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'focus' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.category' => ['required', 'string', 'in:'.implode(',', self::CATEGORIES)],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.target' => ['nullable', 'string', 'max:255'],
            'items.*.frequency' => ['nullable', 'string', 'max:255'],
            'items.*.duration' => ['nullable', 'string', 'max:255'],
            'items.*.instructions' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'status' => ['nullable', 'string', 'max:50'],
            'follow_up_date' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Add at least one lifestyle item.',
            'items.min' => 'Add at least one lifestyle item.',
            'items.*.category.in' => 'Lifestyle category must be one of: sleep, exercise, nutrition, stress, social, other.',
        ];
    }

    /**
     * Attributes validated for storage (patient + prescriber come from the route / auth user).
     *
     * @return array<string, mixed>
     */
    public function validatedForStorage(): array
    {
        return [
            'focus' => $this->input('focus'),
            'items' => $this->input('items'),
            'notes' => $this->input('notes'),
            'status' => $this->input('status') ?? 'Draft',
            'follow_up_date' => $this->input('follow_up_date'),
        ];
    }
}
