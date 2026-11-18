<?php

namespace App\Http\Requests;

use App\Enums\LanguageEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFacultyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'foundation_id'       => ['required', 'integer', 'exists:foundations,id'],
            'name_ar'             => ['required', 'string', 'max:255'],
            'name_fr'             => ['required', 'string', 'max:255'],
            'default_language'    => ['required', Rule::enum(LanguageEnum::class)],
            'address'             => ['nullable', 'string', 'max:255'],
            'signatory_name'      => ['nullable', 'string', 'max:255'],
            'signatory_title_ar'  => ['nullable', 'string', 'max:255'],
            'signatory_title_fr'  => ['nullable', 'string', 'max:255'],
        ];
    }
}
