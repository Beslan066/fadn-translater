@extends('layouts.main')

@section('title', 'Управление очередями')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-0">Управление очередями заданий</h1>
            </div>
        </div>

        {{-- Статистика --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Всего заданий в очереди
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-jobs">
                                    {{ $stats['pending_jobs'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tasks fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Активные воркеры
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="active-workers">
                                    {{ collect($workers)->where('running', true)->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-play-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Неудачные задания
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="failed-jobs">
                                    {{ $stats['failed_jobs'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Обработано сегодня
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="processed-today">
                                    {{ \App\Models\QueueJobLog::whereDate('created_at', today())->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Управление воркерами --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Управление воркерами</h6>
                    </div>
                    <div class="card-body">
                        <div class="row" id="workers-container">
                            @foreach($workers as $queue => $status)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                Очередь: <strong>{{ $queue }}</strong>
                                                <span class="badge badge-{{ $status['running'] ? 'success' : 'danger' }} float-right">
                                            {{ $status['running'] ? 'Запущен' : 'Остановлен' }}
                                        </span>
                                            </h5>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    PID: {{ $status['pid'] ?? 'N/A' }}<br>
                                                    Uptime: {{ $status['uptime'] ?? 'N/A' }}<br>
                                                    Обработано: {{ $status['processed_jobs'] ?? 0 }}<br>
                                                    Ошибок: {{ $status['failed_jobs'] ?? 0 }}
                                                </small>
                                            </p>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-success btn-start" data-queue="{{ $queue }}"
                                                    {{ $status['running'] ? 'disabled' : '' }}>
                                                    <i class="fas fa-play"></i> Запустить
                                                </button>
                                                <button class="btn btn-warning btn-restart" data-queue="{{ $queue }}">
                                                    <i class="fas fa-redo"></i> Перезапустить
                                                </button>
                                                <button class="btn btn-danger btn-stop" data-queue="{{ $queue }}"
                                                    {{ !$status['running'] ? 'disabled' : '' }}>
                                                    <i class="fas fa-stop"></i> Остановить
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Логи заданий --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Логи выполнения заданий</h6>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" id="refresh-logs">
                                <i class="fas fa-sync-alt"></i> Обновить
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-bordered table-hover" id="logs-table">
                                <thead class="thead-light sticky-top">
                                <tr>
                                    <th>Время</th>
                                    <th>Тип задания</th>
                                    <th>Очередь</th>
                                    <th>Статус</th>
                                    <th>Время выполнения</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody id="logs-body">
                                @forelse($logs as $log)
                                    <tr class="
                                    @if($log->status === 'completed') table-success
                                    @elseif($log->status === 'failed') table-danger
                                    @elseif($log->status === 'processing') table-warning
                                    @endif">
                                        <td>{{ optional($log->created_at)->format('H:i:s') ?? '—' }}</td>
                                        <td>
                                            <small>{{ class_basename($log->job_type) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $log->queue ?? 'default' }}</span>
                                        </td>
                                        <td>
                                        <span class="badge badge-{{
                                            ($log->status === 'completed') ? 'success' :
                                            (($log->status === 'failed') ? 'danger' : 'warning')
                                        }}">
                                            {{ $log->status ?? 'unknown' }}
                                        </span>
                                        </td>
                                        <td>
                                            @if($log->execution_time)
                                                {{ $log->execution_time }} сек.
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-view-log" data-log-id="{{ $log->id }}">
                                                <i class="fas fa-eye"></i> Детали
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Нет логов для отображения</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Модальное окно для деталей лога --}}
    <div class="modal fade" id="logDetailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Детали задания</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <pre id="log-details-content" style="max-height: 400px; overflow-y: auto;"></pre>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Обновление статуса каждые 10 секунд
            function updateStatus() {
                $.get('/queue/status', function(data) {
                    // Обновляем UI на основе статуса
                });
            }

            // Запуск воркера
            $('.btn-start').click(function() {
                const queue = $(this).data('queue');
                const button = $(this);

                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Запуск...');

                $.ajax({
                    url: '/queue/start',
                    method: 'POST',
                    data: {
                        queue: queue,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showToast('success', response.message);
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function(xhr) {
                        showToast('error', xhr.responseJSON?.message || 'Ошибка запуска');
                        button.prop('disabled', false).html('<i class="fas fa-play"></i> Запустить');
                    }
                });
            });

            // Остановка воркера
            $('.btn-stop').click(function() {
                const queue = $(this).data('queue');
                const button = $(this);

                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Остановка...');

                $.ajax({
                    url: '/queue/stop',
                    method: 'POST',
                    data: {
                        queue: queue,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showToast('success', response.message);
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function(xhr) {
                        showToast('error', xhr.responseJSON?.message || 'Ошибка остановки');
                        button.prop('disabled', false).html('<i class="fas fa-stop"></i> Остановить');
                    }
                });
            });

            // Перезапуск воркера
            $('.btn-restart').click(function() {
                const queue = $(this).data('queue');
                const button = $(this);

                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Перезапуск...');

                $.ajax({
                    url: '/queue/restart',
                    method: 'POST',
                    data: {
                        queue: queue,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showToast('success', response.message);
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function(xhr) {
                        showToast('error', xhr.responseJSON?.message || 'Ошибка перезапуска');
                        button.prop('disabled', false).html('<i class="fas fa-redo"></i> Перезапустить');
                    }
                });
            });

            // Просмотр деталей лога
            $('.btn-view-log').click(function() {
                const logId = $(this).data('log-id');

                $.get('/queue/logs/' + logId, function(data) {
                    $('#log-details-content').text(JSON.stringify(data, null, 2));
                    $('#logDetailsModal').modal('show');
                });
            });

            // Обновление логов
            $('#refresh-logs').click(function() {
                const button = $(this);
                button.html('<i class="fas fa-spinner fa-spin"></i>');

                $.get('/queue/logs', function(logs) {
                    const tbody = $('#logs-body');
                    tbody.empty();

                    logs.forEach(log => {
                        const statusClass = {
                            'completed': 'success',
                            'failed': 'danger',
                            'processing': 'warning'
                        }[log.status] || 'secondary';

                        tbody.append(`
                    <tr class="table-${statusClass}">
                        <td>${new Date(log.created_at).toLocaleTimeString()}</td>
                        <td><small>${log.job_type.split('\\').pop()}</small></td>
                        <td><span class="badge badge-secondary">${log.queue}</span></td>
                        <td><span class="badge badge-${statusClass}">${log.status}</span></td>
                        <td>${log.execution_time ? log.execution_time + ' сек.' : '—'}</td>
                        <td>
                            <button class="btn btn-sm btn-info btn-view-log" data-log-id="${log.id}">
                                <i class="fas fa-eye"></i> Детали
                            </button>
                        </td>
                    </tr>
                `);
                    });

                    button.html('<i class="fas fa-sync-alt"></i> Обновить');
                });
            });

            // Авто-обновление каждые 30 секунд
            setInterval(() => {
                if ($('#refresh-logs').is(':visible')) {
                    $('#refresh-logs').click();
                }
            }, 30000);

            function showToast(type, message) {
                // Используйте toast библиотеку или создайте свою реализацию
                alert(message); // Временно
            }
        });
    </script>
@endpush
