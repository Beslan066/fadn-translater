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
                                <option value="0">Основной корпус</option>
                                <option value="1">Дополнительный корпус</option>
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
    <script>
        // Глобальные переменные
        let currentExportId = null;
        let intensiveCheckInterval = null;

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

            const container = document.getElementById('notifications-container') || document.body;
            const toastElement = document.createElement('div');
            toastElement.innerHTML = toastHtml;
            container.appendChild(toastElement);

            const toast = toastElement.querySelector('.toast');
            const bsToast = new bootstrap.Toast(toast, {
                delay: delay
            });
            bsToast.show();

            if (delay > 0) {
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, delay + 1000);
            }

            return bsToast;
        }

        // Надежная функция скачивания файла
        function downloadFile(downloadUrl, exportId) {
            console.log("Downloading:", downloadUrl);

            $('#exportModal').modal('hide'); // если модалка открыта

            setTimeout(() => {
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.setAttribute('download', '');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // локальное кеширование
                const downloadedExports = JSON.parse(localStorage.getItem('downloadedExports') || '[]');
                if (!downloadedExports.includes(exportId)) {
                    downloadedExports.push(exportId);
                    localStorage.setItem('downloadedExports', JSON.stringify(downloadedExports));
                }
            }, 300);
        }

        // Альтернативный метод скачивания через создание ссылки
        function downloadFileAlternative(fileName, exportId) {
            return new Promise((resolve, reject) => {
                try {
                    const link = document.createElement('a');
                    link.href = `{{ url('download-export') }}/${fileName}`;
                    link.download = fileName;
                    link.style.display = 'none';

                    link.onclick = function() {
                        // Помечаем файл как скачанный
                        const downloadedExports = JSON.parse(localStorage.getItem('downloadedExports') || '[]');
                        if (!downloadedExports.includes(exportId)) {
                            downloadedExports.push(exportId);
                            localStorage.setItem('downloadedExports', JSON.stringify(downloadedExports));
                        }

                        setTimeout(() => {
                            resolve();
                        }, 1000);
                    };

                    document.body.appendChild(link);
                    link.click();

                    // Удаляем ссылку через некоторое время
                    setTimeout(() => {
                        if (link.parentNode) {
                            document.body.removeChild(link);
                        }
                    }, 5000);

                } catch (error) {
                    reject(error);
                }
            });
        }

        // Основная функция скачивания
        async function downloadExportFile(fileName, exportId) {
            try {
                console.log('Starting download process for:', fileName);

                showToastNotification('Подготовка', `Подготовка файла ${fileName} к скачиванию...`, 'info', 3000);

                // Ждем немного чтобы файл точно был готов
                await new Promise(resolve => setTimeout(resolve, 1000));

                // Пробуем первый метод (iframe)
                try {
                    await downloadFile(fileName, exportId);
                    showToastNotification('Успех', `Файл ${fileName} успешно скачан.`, 'success', 5000);
                    return;
                } catch (error) {
                    console.log('First download method failed, trying alternative...');
                }

                // Пробуем второй метод (ссылка)
                try {
                    await downloadFileAlternative(fileName, exportId);
                    showToastNotification('Успех', `Файл ${fileName} отправлен на скачивание.`, 'success', 5000);
                    return;
                } catch (error) {
                    console.log('Second download method failed...');
                }

                // Если оба метода не сработали, предлагаем ручное скачивание
                throw new Error('Автоматическое скачивание не сработало');

            } catch (error) {
                console.error('Download error:', error);

                // Показываем кнопку для ручного скачивания
                showManualDownloadOption(fileName, exportId);

                showToastNotification(
                    'Скачайте вручную',
                    'Нажмите на кнопку "Скачать вручную" для загрузки файла.',
                    'warning',
                    8000
                );
            }
        }

        // Функция для показа опции ручного скачивания
        function showManualDownloadOption(fileName, exportId) {
            // Сначала удаляем предыдущие уведомления о ручном скачивании
            document.querySelectorAll('.manual-download-toast').forEach(toast => {
                toast.remove();
            });

            const manualDownloadHtml = `
            <div class="bs-toast toast toast-placement-ex m-2 fade bg-warning manual-download-toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="icon-base ri ri-download-line me-2"></i>
                    <div class="me-auto fw-semibold">Скачать вручную</div>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <p>Файл <strong>${fileName}</strong> готов к скачиванию.</p>
                    <div class="d-flex gap-2 mt-2">
                        <a href="{{ url('download-export') }}/${fileName}"
                           class="btn btn-sm btn-primary"
                           download
                           onclick="markAsDownloaded('${exportId}')">
                            <i class="icon-base ri ri-download-line me-1"></i> Скачать
                        </a>
                        <button class="btn btn-sm btn-secondary" onclick="this.closest('.toast').remove()">
                            <i class="icon-base ri ri-close-line me-1"></i> Закрыть
                        </button>
                    </div>
                </div>
            </div>
        `;

            const container = document.getElementById('notifications-container');
            const toastElement = document.createElement('div');
            toastElement.innerHTML = manualDownloadHtml;
            container.appendChild(toastElement);

            const toast = toastElement.querySelector('.toast');
            new bootstrap.Toast(toast, { delay: 0 }).show();
        }

        // Функция для отметки скачанных файлов
        function markAsDownloaded(exportId) {
            const downloadedExports = JSON.parse(localStorage.getItem('downloadedExports') || '[]');
            if (!downloadedExports.includes(exportId)) {
                downloadedExports.push(exportId);
                localStorage.setItem('downloadedExports', JSON.stringify(downloadedExports));
            }

            // Удаляем уведомление о ручном скачивании
            setTimeout(() => {
                document.querySelectorAll('.manual-download-toast').forEach(toast => {
                    toast.remove();
                });
            }, 1000);
        }

        // Функция для проверки статуса экспорта
        async function checkExportStatus() {
            try {
                const response = await fetch('/export-status', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'include'
                });

                if (!response.ok) return;
                const data = await response.json();

                if (!data.success) return;

                // Берём завершённые экспорты
                const completedExports = data.exports.filter(exp => exp.status === 'completed' && exp.file_exists);

                completedExports.forEach(exp => {
                    const downloaded = JSON.parse(localStorage.getItem('downloadedExports') || '[]');
                    if (!downloaded.includes(exp.id) && exp.download_url) {
                        // Скачиваем файл
                        const link = document.createElement('a');
                        link.href = exp.download_url;
                        link.download = '';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        downloaded.push(exp.id);
                        localStorage.setItem('downloadedExports', JSON.stringify(downloaded));
                    }
                });

            } catch (error) {
                console.error('Ошибка проверки экспорта:', error);
            }
        }

        // Проверка каждые 5 секунд
        setInterval(checkExportStatus, 5000);

        // Функция для интенсивной проверки статуса
        function startIntensiveStatusChecking() {
            let checkCount = 0;
            const maxChecks = 120;

            // Останавливаем предыдущий интервал если есть
            if (intensiveCheckInterval) {
                clearInterval(intensiveCheckInterval);
            }

            showToastNotification('Экспорт начался', 'Файл готовится. Это может занять несколько минут...', 'info', 8000);

            intensiveCheckInterval = setInterval(async () => {
                checkCount++;

                if (checkCount >= maxChecks) {
                    clearInterval(intensiveCheckInterval);
                    showToastNotification('Время ожидания истекло', 'Экспорт занимает больше времени чем ожидалось.', 'warning');
                    return;
                }

                try {
                    const response = await fetch('{{ route("export.status") }}', {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'include'
                    });

                    if (response.ok) {
                        const data = await response.json();

                        if (data.success && data.exports && data.exports.length > 0) {
                            const completedExports = data.exports.filter(exp =>
                                exp.status === 'completed' && exp.file_exists === true
                            );

                            if (completedExports.length > 0) {
                                clearInterval(intensiveCheckInterval);
                                const exportItem = completedExports[0];
                                await downloadExportFile(exportItem.file_name, exportItem.id);
                            }
                        }
                    }
                } catch (error) {
                    console.log('Интенсивная проверка:', error.message);
                }
            }, 5000);
        }

        // Основной код
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Страница регионов загружена');

            // Обработчик клика по кнопке экспорта
            document.querySelectorAll('.export-trigger').forEach(button => {
                button.addEventListener('click', function() {
                    const regionId = this.getAttribute('data-region-id');
                    document.getElementById('exportRegionId').value = regionId;
                });
            });

            // Обработчик отправки формы экспорта
            const exportForm = document.getElementById('exportForm');
            if (exportForm) {
                exportForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const form = this;
                    const submitButton = form.querySelector('button[type="submit"]');
                    const spinner = document.getElementById('exportSpinner');
                    const exportModal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));

                    // Показываем спиннер
                    spinner.classList.remove('d-none');
                    submitButton.disabled = true;

                    const formData = new FormData(form);
                    const regionId = formData.get('region_id');

                    try {
                        const response = await fetch(`/regions/${regionId}/export-sentences`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Закрываем модальное окно экспорта
                            exportModal.hide();

                            // Показываем уведомление о запуске
                            showToastNotification('Экспорт запущен', 'Начинается процесс экспорта данных. Файл будет скачан автоматически после завершения.', 'info');

                            // Запускаем интенсивную проверку статуса
                            startIntensiveStatusChecking();

                        } else {
                            throw new Error(data.message || 'Неизвестная ошибка');
                        }
                    } catch (error) {
                        console.error('Ошибка:', error);
                        showToastNotification('Ошибка', 'Произошла ошибка при запуске экспорта.', 'error');
                    } finally {
                        // Скрываем спиннер и активируем кнопку
                        spinner.classList.add('d-none');
                        submitButton.disabled = false;
                    }
                });
            }

            // Периодическая проверка статуса каждые 30 секунд
            setInterval(checkExportStatus, 30000);

            // Первая проверка через 5 секунд после загрузки
            setTimeout(checkExportStatus, 5000);
        });
    </script>
@endpush

