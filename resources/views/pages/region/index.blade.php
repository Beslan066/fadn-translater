@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        .spinner-icon {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }

        @keyframes progress-bar-stripes {
            0% { background-position: 1rem 0; }
            100% { background-position: 0 0; }
        }

        .export-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-datatable text-nowrap">
            <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                <div class="row card-header mx-0 px-2">
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                        <h5 class="card-title mb-0">Список регионов</h5>
                    </div>
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto">
                        <div class="dt-buttons btn-group flex-wrap">
                            <div class="btn-group">
                                <button class="btn buttons-collection btn-label-primary dropdown-toggle me-4 waves-effect border-none"
                                        tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="dialog"
                                        aria-expanded="false" data-bs-toggle="dropdown">
                                    <span><span class="d-flex align-items-center gap-2"><i
                                                class="icon-base ri ri-external-link-line icon-18px"></i> <span
                                                class="d-none d-sm-inline-block">Экспорт</span></span></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('regions.export') }}">Экспорт регионов в CSV</a></li>
                                </ul>
                            </div>
                            <a href="{{route('regions.create')}}" class="btn create-new btn-primary" tabindex="0"
                               aria-controls="DataTables_Table_0" type="button">
                                <span><span class="d-flex align-items-center"><i
                                            class="icon-base ri ri-add-line icon-18px me-sm-1"></i><span
                                            class="d-none d-sm-inline-block">Добавить</span></span></span>
                            </a>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <form method="GET" action="{{ route('regions.index') }}">
                    <div class="row m-3 mx-2 my-0 justify-content-between">
                        <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mb-2 mt-2">
                            <div class="dt-search d-flex align-items-center">
                                <input type="search" name="search" value="{{ request('search') }}"
                                       class="form-control form-control-sm" id="dt-search-0"
                                       placeholder="Поиск по названию или коду" aria-controls="DataTables_Table_0"
                                       style="border:1px solid #d1cfd4 !important; width: 250px;">
                                <button type="submit" class="btn btn-sm btn-primary ms-2">
                                    <i class="icon-base ri ri-search-line"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('regions.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                                        <i class="icon-base ri ri-close-line"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
                <div class="justify-content-between dt-layout-table">
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-full table-responsive">
                        <table class="datatables-basic table table-bordered table-responsive dataTable dtr-column"
                               id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info" style="width: 100%;">
                            <thead>
                            <tr>
                                <th data-dt-column="0" class="control dt-orderable-none dtr-hidden" rowspan="1" colspan="1" aria-label="" style="display: none;"></th>
                                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-select dt-orderable-none" aria-label="">ID</th>
                                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc" aria-label="Name: Activate to sort" tabindex="0">
                                    <span class="dt-column-title" role="button">Регион</span>
                                </th>
                                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc" aria-label="Email: Activate to sort" tabindex="0">
                                    <span class="dt-column-title" role="button">Пользователи</span>
                                </th>
                                <th data-dt-column="6" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc" aria-label="Date: Activate to sort" tabindex="0">
                                    <span class="dt-column-title" role="button">Переводчики</span>
                                </th>
                                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc" aria-label="Status: Activate to sort" tabindex="0">
                                    <span class="dt-column-title" role="button">Корректоры</span>
                                </th>
                                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc" aria-label="Status: Activate to sort" tabindex="0">
                                    <span class="dt-column-title" role="button">Переведено</span>
                                </th>
                                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc" aria-label="Status: Activate to sort" tabindex="0">
                                    <span class="dt-column-title" role="button">На проверке</span>
                                </th>
                                <th data-dt-column="8" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc" aria-label="Status: Activate to sort" tabindex="0">
                                    <span class="dt-column-title" role="button">Статус</span>
                                </th>
                                <th class="dt-orderable-none" data-dt-column="9" rowspan="1" colspan="1" aria-label="Actions">
                                    <span class="dt-column-title">Дата</span>
                                </th>
                                <th class="dt-orderable-none" data-dt-column="9" rowspan="1" colspan="1" aria-label="Actions">
                                    <span class="dt-column-title">Действия</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($regions as $region)
                                @php
                                    $stats = $region->getTranslationStats();
                                @endphp
                                <tr>
                                    <td class="dt-select">{{ $region->id }}</td>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center user-name">
                                            <div class="d-flex flex-column">
                                                <span class="emp_name text-truncate h6 mb-0">{{ $region->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $region->users->count() }}</td>
                                    <td>{{ $region->translators->count() }}</td>
                                    <td>{{ $region->proofreaders->count() }}</td>
                                    <td>{{ $stats['translated'] }}</td>
                                    <td>{{ $stats['proofread'] }}</td>
                                    <td>
                                        @if($region->is_active)
                                            <span class="badge rounded-pill bg-label-success">Активен</span>
                                        @else
                                            <span class="badge rounded-pill bg-label-danger">Неактивен</span>
                                        @endif
                                    </td>
                                    <td>{{ $region->created_at->format('d.m.Y') }}</td>
                                    <td class="d-flex align-items-center">
                                        <div class="d-inline-block">
                                            <a href="javascript:;"
                                               class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                                               data-bs-toggle="dropdown">
                                                <i class="icon-base ri ri-more-2-line icon-22px"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end m-0">
                                                <li>
                                                    <a href="javascript:;"
                                                       class="dropdown-item export-trigger"
                                                       data-region-id="{{ $region->id }}"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#exportModal">
                                                        Экспорт корпуса
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a href="{{ route('regions.edit', $region->id) }}" class="dropdown-item">
                                                        Редактировать
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('regions.destroy', $region->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Вы уверены что хотите удалить этот регион?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            Удалить
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="{{ route('regions.edit', $region->id) }}"
                                           class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit">
                                            <i class="icon-base ri ri-edit-box-line icon-22px"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mx-3 justify-content-between">
                    {{ $regions->appends(['search' => request('search')])->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно экспорта -->
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Экспорт корпуса предложений</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="exportForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="region_id" id="exportRegionId">

                        <div class="mb-3">
                            <label class="form-label">Тип предложения</label>
                            <select class="form-select" name="other_sentence" id="exportSentenceType">
                                <option value="">Все предложения</option>
                                <option value="0">Основной корпус</option>  <!-- было value="0" -->
                                <option value="1">Дополнительный корпус</option>  <!-- было value="1" -->
                            </select>
                        </div>

                        <div class="alert alert-info">
                            <i class="icon-base ri ri-information-line me-2"></i>
                            Экспорт будет выполнен в фоновом режиме. Файл скачается автоматически после завершения.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="exportSpinner"></span>
                            Начать экспорт
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Контейнер для уведомлений -->
    <div id="notifications-container" class="export-notification"></div>
@endsection

@push('scripts')
    // Полностью замените скрипт на странице регионов на этот:

    <script>
        // Глобальные переменные
        let intensiveCheckInterval = null;
        let isDownloading = false;

        // Функция для показа уведомлений
        function showToastNotification(title, message, type = 'info', delay = 5000) {
            const bgClass = type === 'error' ? 'bg-danger' : type === 'success' ? 'bg-success' : type === 'warning' ? 'bg-warning' : 'bg-info';
            const icon = type === 'error' ? 'ri-error-warning-line' : type === 'success' ? 'ri-checkbox-circle-line' : type === 'warning' ? 'ri-alert-line' : 'ri-information-line';

            const toastHtml = `
            <div class="bs-toast toast toast-placement-ex m-2 fade ${bgClass}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="icon-base ri ${icon} me-2"></i>
                    <div class="me-auto fw-semibold">${title}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `;

            const container = document.getElementById('notifications-container');
            const toastElement = document.createElement('div');
            toastElement.innerHTML = toastHtml;
            container.appendChild(toastElement);

            const toast = toastElement.querySelector('.toast');
            const bsToast = new bootstrap.Toast(toast, { delay: delay });
            bsToast.show();

            setTimeout(() => {
                if (toastElement.parentNode) {
                    toastElement.remove();
                }
            }, delay + 1000);
        }

        // Функция принудительного скачивания файла
        function forceDownloadFile(fileName, exportId) {
            console.log('Force downloading:', fileName);

            // Прямая ссылка на скачивание
            const downloadUrl = `/download-export-direct/${fileName}`;

            // Создаем невидимую ссылку и кликаем
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = fileName;
            link.style.display = 'none';
            document.body.appendChild(link);

            // Добавляем обработчик для отслеживания
            link.onclick = function() {
                console.log('Download clicked for:', fileName);
                // Отмечаем как скачанный
                const downloaded = JSON.parse(localStorage.getItem('downloadedExports') || '[]');
                if (!downloaded.includes(exportId)) {
                    downloaded.push(exportId);
                    localStorage.setItem('downloadedExports', JSON.stringify(downloaded));
                }
            };

            link.click();

            // Удаляем ссылку через секунду
            setTimeout(() => {
                if (link.parentNode) {
                    document.body.removeChild(link);
                }
            }, 1000);

            showToastNotification('Скачивание начато', `Файл ${fileName} загружается...`, 'success', 3000);
        }

        // Проверка статуса экспорта
        async function checkExportStatus() {
            if (isDownloading) return;

            try {
                const response = await fetch('/export-status', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) return;

                const data = await response.json();
                console.log('Export status check:', data);

                if (!data.success) return;

                // Получаем завершенные экспорты
                const completedExports = data.exports.filter(exp =>
                    exp.status === 'completed' && exp.file_exists === true
                );

                console.log('Completed exports found:', completedExports.length);

                // Проверяем какие еще не скачаны
                const downloadedExports = JSON.parse(localStorage.getItem('downloadedExports') || '[]');

                for (const exp of completedExports) {
                    if (!downloadedExports.includes(exp.id)) {
                        console.log('Starting download for export:', exp.id, exp.file_name);
                        isDownloading = true;

                        // Скачиваем файл
                        forceDownloadFile(exp.file_name, exp.id);

                        // Показываем уведомление
                        showToastNotification(
                            'Экспорт готов',
                            `Файл "${exp.file_name}" успешно сформирован и начал скачивание.`,
                            'success',
                            5000
                        );

                        // Останавливаем интенсивную проверку если была
                        if (intensiveCheckInterval) {
                            clearInterval(intensiveCheckInterval);
                            intensiveCheckInterval = null;
                        }

                        setTimeout(() => {
                            isDownloading = false;
                        }, 3000);

                        break; // Скачиваем по одному файлу за раз
                    }
                }
            } catch (error) {
                console.error('Ошибка проверки экспорта:', error);
                isDownloading = false;
            }
        }

        // Интенсивная проверка после запуска экспорта
        function startIntensiveStatusChecking() {
            // Останавливаем предыдущий интервал
            if (intensiveCheckInterval) {
                clearInterval(intensiveCheckInterval);
            }

            let checkCount = 0;
            const maxChecks = 60; // 5 минут максимум

            showToastNotification('Экспорт запущен', 'Файл готовится. Это может занять несколько минут...', 'info', 8000);

            intensiveCheckInterval = setInterval(() => {
                checkCount++;
                console.log(`Intensive check ${checkCount}/${maxChecks}`);

                if (checkCount >= maxChecks) {
                    clearInterval(intensiveCheckInterval);
                    intensiveCheckInterval = null;
                    showToastNotification('Время ожидания', 'Экспорт занимает больше времени. Проверьте позже в разделе экспортов.', 'warning');
                }

                checkExportStatus();
            }, 5000); // Проверяем каждые 5 секунд
        }

        // Создаем плавающую кнопку для ручного скачивания
        function showFloatingDownloadButton(fileName, exportId) {
            let floatBtn = document.getElementById('floating-download-btn');

            if (!floatBtn) {
                floatBtn = document.createElement('div');
                floatBtn.id = 'floating-download-btn';
                floatBtn.innerHTML = `
                <div style="position: fixed; bottom: 20px; right: 20px; z-index: 10000;">
                    <button class="btn btn-success btn-lg shadow" style="border-radius: 50px; padding: 12px 24px;">
                        <i class="ri-download-line me-2"></i>
                        Скачать экспорт
                    </button>
                </div>
            `;
                document.body.appendChild(floatBtn);

                floatBtn.onclick = () => {
                    forceDownloadFile(fileName, exportId);
                    floatBtn.remove();
                };
            } else {
                floatBtn.onclick = () => {
                    forceDownloadFile(fileName, exportId);
                    floatBtn.remove();
                };
            }

            // Авто-скрытие через 1 минуту
            setTimeout(() => {
                if (floatBtn && floatBtn.parentNode) {
                    floatBtn.remove();
                }
            }, 60000);
        }

        // Обработчик формы экспорта
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Страница регионов загружена');

            // Установка CSRF токена для всех fetch запросов
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Обработчики для кнопок экспорта
            document.querySelectorAll('.export-trigger').forEach(button => {
                button.addEventListener('click', function() {
                    const regionId = this.getAttribute('data-region-id');
                    document.getElementById('exportRegionId').value = regionId;
                });
            });

            // Отправка формы экспорта
            const exportForm = document.getElementById('exportForm');
            if (exportForm) {
                exportForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const form = this;
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const spinner = document.getElementById('exportSpinner');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));

                    // Блокируем кнопку
                    submitBtn.disabled = true;
                    spinner.classList.remove('d-none');

                    const formData = new FormData(form);
                    const regionId = formData.get('region_id');

                    try {
                        const response = await fetch(`/regions/${regionId}/export-sentences`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const data = await response.json();
                        console.log('Export response:', data);

                        if (data.success) {
                            // Закрываем модалку
                            modal.hide();

                            // Показываем уведомление
                            showToastNotification('Экспорт запущен', data.message, 'info');

                            // Запускаем интенсивную проверку
                            startIntensiveStatusChecking();

                            // Также запускаем обычную периодическую проверку
                            setTimeout(() => {
                                checkExportStatus();
                            }, 2000);
                        } else {
                            throw new Error(data.message || 'Ошибка запуска экспорта');
                        }
                    } catch (error) {
                        console.error('Export error:', error);
                        showToastNotification('Ошибка', error.message, 'error');
                    } finally {
                        submitBtn.disabled = false;
                        spinner.classList.add('d-none');
                    }
                });
            }

            // Запускаем проверку статуса сразу
            checkExportStatus();

            // И каждые 10 секунд
            setInterval(checkExportStatus, 10000);
        });
    </script>
@endpush

