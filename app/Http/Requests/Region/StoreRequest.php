<?php

namespace App\Http\Requests\Region;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name' => 'required|string',
            'code' => 'required|string',
            'language_code' => 'required|string',
            'user_id' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Заголовок обязателен для заполнения',
            'name.max' => 'Длина заголовка не должна превышать 255 символов',
            'code.required' => 'Код региона обязателен для заполнения',
            'language_code.required' => 'Код языка обязателен для заполнения',
        ];
    }
}
