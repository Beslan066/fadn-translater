<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf


        <div class="mt-4 flex flex-col items-center">
            <div class="relative w-32 h-32 mb-4 rounded-full overflow-hidden border-2 border-gray-300 bg-gray-100">
                <!-- Preview Image -->
                <img id="avatar-preview" src="{{ asset('assets/img/user.png') }}"
                     alt="Avatar Preview"
                     class="w-full h-full object-cover">

                <!-- Upload Overlay -->
                <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity bg-black bg-opacity-50 cursor-pointer">
                    <span class="text-white text-sm font-medium">Изменить</span>
                </div>
            </div>

            <!-- Hidden File Input -->
            <x-text-input id="avatar" name="avatar" type="file" class="hidden" />

            <x-input-label for="avatar" class="cursor-pointer bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                {{ __('Выбрать аватар') }}
            </x-input-label>
            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('ФИО')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>


        <div class="mt-4">
            <x-select id="select" class="block w-full" name="region_id">
                <option value="" selected disabled hidden>{{ __('Выберите регион') }}</option>
                @foreach($regions as $region)
                    <option value="{{$region->id}}">
                        {{$region->name}}
                    </option>
                @endforeach
            </x-select>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Пароль')" />

            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Подтвердите пароль')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>


        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Уже зарегистрированы?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Зарегистрироваться') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
