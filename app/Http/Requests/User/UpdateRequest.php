<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;


class UpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'region_id' => 'nullable|exists:regions,id',
            'role' => 'required|in:fadn,region_admin,translator,proofreader,user,super_admin',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ФИО обязательно для заполнения',
            'email.required' => 'Email обязателен для заполнения',
            'email.unique' => 'Этот email уже занят другим пользователем',
            'password.confirmed' => 'Пароли не совпадают',
            'region_id.required' => 'Необходимо выбрать регион',
            'role.required' => 'Необходимо выбрать роль',
        ];
    }
}
