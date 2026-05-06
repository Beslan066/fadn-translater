<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация ожидает подтверждения</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <div class="text-center">
            <!-- Иконка часов/ожидания -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                Регистрация успешно завершена!
            </h2>

            <p class="text-gray-600 mb-6">
                Спасибо за регистрацию на платформе.
            </p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-left">
                <p class="text-blue-800 font-semibold mb-2">
                    ⏳ Что дальше?
                </p>
                <p class="text-blue-700 text-sm mb-3">
                    Ваш аккаунт ожидает подтверждения от администратора региона.
                    После подтверждения вам станет доступен весь функционал платформы.
                </p>

                @if(isset($regionName))
                    <p class="text-blue-700 text-sm mt-2">
                        📍 <strong>Ваш регион:</strong> {{ $regionName }}
                    </p>
                @endif

                @if(isset($regionAdminEmail))
                    <div class="mt-3 pt-3 border-t border-blue-200">
                        <p class="text-blue-700 text-sm">
                            📧 <strong>Контактный email администратора региона:</strong><br>
                            <a href="mailto:{{ $regionAdminEmail }}" class="text-blue-600 underline hover:text-blue-800">
                                {{ $regionAdminEmail }}
                            </a>
                        </p>
                        @if(isset($regionAdminName))
                            <p class="text-blue-700 text-sm mt-1">
                                👤 <strong>Администратор:</strong> {{ $regionAdminName }}
                            </p>
                        @endif
                    </div>
                @else
                    <p class="text-orange-600 text-sm mt-2">
                        ⚠️ Администратор региона пока не назначен. Пожалуйста, обратитесь в поддержку.
                    </p>
                @endif
            </div>

            <div class="text-sm text-gray-500 mb-6">
                <p class="mt-1">Если у вас есть вопросы, свяжитесь с администратором вашего региона.</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
