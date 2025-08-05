<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;


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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => ['required', Rules\Password::defaults()],
            'region_id' => 'nullable|exists:regions,id',
            'role' => 'required|in:fadn,region_admin,translator,proofreader,user,super_admin',
            'is_active' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            // Для поля name
            'name.required' => 'Поле "Имя" обязательно для заполнения',
            'name.string' => 'Поле "Имя" должно быть строкой',
            'name.max' => 'Поле "Имя" не должно превышать 255 символов',

            // Для поля email
            'email.required' => 'Поле "Email" обязательно для заполнения',
            'email.string' => 'Поле "Email" должно быть строкой',
            'email.email' => 'Введите корректный email адрес',
            'email.max' => 'Поле "Email" не должно превышать 255 символов',
            'email.unique' => 'Этот email уже занят другим пользователем',

            // Для поля avatar
            'avatar.image' => 'Файл должен быть изображением',
            'avatar.mimes' => 'Изображение должно быть в формате: jpeg, png, jpg или svg',
            'avatar.max' => 'Размер изображения не должен превышать 2MB',

            // Для поля password
            'password.required' => 'Поле "Пароль" обязательно для заполнения',
            'password.min' => 'Пароль должен содержать минимум :min символов',
            'password.mixed' => 'Пароль должен содержать буквы разного регистра',
            'password.letters' => 'Пароль должен содержать буквы',
            'password.numbers' => 'Пароль должен содержать цифры',
            'password.symbols' => 'Пароль должен содержать специальные символы',
            'password.uncompromised' => 'Этот пароль слишком простой или часто используется',

            // Для поля region_id
            'region_id.required' => 'Необходимо выбрать регион',
            'region_id.exists' => 'Выбранный регион не существует',

            // Для поля role
            'role.required' => 'Необходимо выбрать роль пользователя',
            'role.in' => 'Выбрана недопустимая роль пользователя',

            // Для поля is_active
            'is_active.boolean' => 'Некорректное значение для статуса активности',
        ];
    }
}
