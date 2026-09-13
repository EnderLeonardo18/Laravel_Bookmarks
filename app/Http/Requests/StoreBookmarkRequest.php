<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookmarkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'url'              => ['required', 'url'],
            'category'         => ['required', 'string'],
            'status'           => ['required', 'string', 'max:50'],
            'description'      => ['nullable', 'string', 'max:2000'],
            'image_preview'    => ['nullable', 'url', 'max:2000'],
            'progress_note'    => ['nullable', 'string', 'max:255'],
            'progress_url'     => ['nullable', 'url'],

            // Reglas para los links alternativos
            'alternative_urls'   => ['nullable', 'array'],
            'alternative_urls.*' => ['required', 'url', 'max:2000'], // Cada elemento del array debe ser una URL válida
        ];
    }
}
