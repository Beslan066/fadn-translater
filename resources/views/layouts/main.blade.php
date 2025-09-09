<!doctype html>

<html
    lang="ru"
    class="layout-menu-fixed layout-compact"
    data-assets-path="../assets/"
    data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />

    <title>Единая панель переводов корпуса языков России</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('assets/favicon1.ico')}}" />
    <link rel="stylesheet" href="{{asset('css/style.css')}}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{asset('assets/vendor/fonts/iconify-icons.css')}}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css -->

    <link rel="stylesheet" href="{{asset('assets/vendor/libs/node-waves/node-waves.css')}}" />

    <link rel="stylesheet" href="{{asset('assets/vendor/css/core.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/css/demo.css')}}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />

    <!-- endbuild -->

    <link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
    @stack('styles')


    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{asset('assets/vendor/js/helpers.js')}}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config: Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file. -->

    <script src="{{asset('assets/js/config.js')}}"></script>
</head>

<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme d-flex flex-direction-column justify-content-between">
            <div>
                <a href="{{route('home')}}">
                    <div class="app-brand demo">
                        <a href="{{route('home')}}" class="app-brand-link">
                            <img src="{{asset('assets/img/logo.svg')}}" style="min-height: 60px"/>
                        </a>
                        <span>
                        Единая панель переводов
                    </span>
                    </div>
                </a>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- User interface -->

                    @if(in_array(auth()->user()->role, ['fadn', 'super_admin']))
                        <li class="menu-item">
                            <a href="{{route('home')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-home-3-line"></i>
                                <div data-i18n="User interface">Главная</div>
                            </a>

                        </li>
                    @elseif(auth()->user()->role === 'region_admin')
                        <li class="menu-item">
                            <a href="{{route('region-admin.index')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-home-3-line"></i>
                                <div>Главная</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{route('region-admin.sentences')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-list-view"></i>
                                <div data-i18n="Icons">Корпус</div>
                            </a>
                        </li>

                        <li class="menu-item">
                            <a href="{{route('region-admin.otherSentences')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-list-view"></i>
                                <div data-i18n="Icons">Доп Корпус</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{route('region-admin.users')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-account-circle-line"></i>
                                <div data-i18n="Icons">Пользователи</div>
                            </a>
                        </li>
                    @elseif(auth()->user()->role === 'translator')
                        <li class="menu-item">
                            <a href="{{route('translator.index')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-home-3-line"></i>
                                <div data-i18n="User interface">Главная</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{route('translator.dashboard')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-keyboard-box-fill"></i>
                                <div data-i18n="Icons">Перевод</div>
                            </a>
                        </li>

                        <li class="menu-item">
                            <a href="{{route('translator.translations')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-check-fill"></i>
                                <div data-i18n="Icons">Прогресс</div>
                            </a>
                        </li>
                    @elseif(auth()->user()->role === 'proofreader')
                        <li class="menu-item">
                            <a href="{{route('proofreader.dashboard')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-home-3-line"></i>
                                <div data-i18n="User interface">Главная</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{route('proofreader.translations')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-check-fill"></i>
                                <div data-i18n="Icons">Переводы</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{route('proofreader.users')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-account-circle-line"></i>
                                <div data-i18n="Icons">Пользователи</div>
                            </a>
                        </li>
                    @endif

                    @if(in_array(auth()->user()->role, ['fadn', 'super_admin']))
                        <li class="menu-item">
                            <a href="{{route('sentences.index')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-file-list-2-line"></i>
                                <div data-i18n="User interface">Корпус</div>
                            </a>

                        </li>

                        <li class="menu-item">
                            <a href="{{route('otherSentences.index')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-list-view"></i>
                                <div data-i18n="Icons">Доп Корпус</div>
                            </a>
                        </li>

                        <!-- Icons -->
                        <li class="menu-item">
                            <a href="{{route('regions.index')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-road-map-line"></i>
                                <div data-i18n="Icons">Регионы</div>
                            </a>
                        </li>


                    @endif

                    @if(in_array(auth()->user()->role, ['fadn', 'super_admin']))
                        <li class="menu-item">
                            <a href="{{route('users.index')}}" class="menu-link">
                                <i class="menu-icon icon-base ri ri-account-circle-line"></i>
                                <div data-i18n="Icons">Пользователи</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <div>
                <ul class="menu-inner py-1">
                    <!-- Forms & Tables -->
                    <li class="menu-header mt-7"><span class="menu-header-text">Система</span></li>

                    <!-- Forms -->
                    <li class="menu-item">
                        <a href="/profile" class="menu-link">
                            <i class="menu-icon icon-base ri ri-user-line icon-md me-3"></i>
                            <div data-i18n="Form Elements">Мой профиль</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link">
                            <i class="menu-icon icon-base ri ri-settings-2-line"></i>
                            <div data-i18n="Form Elements">Настройки</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{route('logout')}}" class="menu-link">
                            <i class="menu-icon icon-base ri ri-logout-box-line"></i>
                            <div data-i18n="Form Elements">Выйти</div>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->

            <nav
                class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
                id="layout-navbar">
                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
                    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                        <i class="icon-base ri ri-menu-line icon-md"></i>
                    </a>

                </div>

                <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
                    <!-- Search -->
                    <div class="navbar-nav align-items-center">
                        <div class="nav-item d-flex align-items-center">
                            <i class="icon-base ri ri-search-line icon-lg lh-0"></i>
                            <input
                                type="text"
                                class="form-control border-0 shadow-none"
                                placeholder="Поиск..."
                                aria-label="Search..." />
                        </div>
                    </div>
                    <!-- /Search -->

                    <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                        <!-- User -->
                        <li class="nav-item navbar-dropdown dropdown-user dropdown">
                            <a
                                class="nav-link dropdown-toggle hide-arrow p-0"
                                href="javascript:void(0);"
                                data-bs-toggle="dropdown">
                                <div class="avatar avatar-online">
                                    @if(isset(auth()->user()->avatar))
                                        <div class="avatar avatar-online">
                                            <img src="{{asset('storage/' . auth()->user()->avatar)}}" alt="alt" class="w-px-40 rounded-circle" style="object-fit: cover" />
                                        </div>
                                    @else
                                        <img src="{{ asset('assets/img/user.png ') }}" alt="alt" class="w-px-40 rounded-circle" style="object-fit: cover" />
                                    @endif                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                @if(isset(auth()->user()->avatar))
                                                    <div class="avatar avatar-online">
                                                        <img src="{{asset('storage/' . auth()->user()->avatar)}}" alt="alt" class="w-px-40 rounded-circle" style="object-fit: cover" />
                                                    </div>
                                                @else
                                                    <img src="{{ asset('assets/img/user.png ') }}" alt="alt" class="w-px-40 rounded-circle" style="object-fit: cover" />
                                                @endif
                                            </div>
                                            @auth()
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{Auth::user()->name}}</h6>
                                                    <small class="text-body-secondary">{{Auth::user()->role}}</small>
                                                </div>
                                            @endauth
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <div class="dropdown-divider my-1"></div>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/profile">
                                        <i class="icon-base ri ri-settings-4-line icon-md me-3"></i>
                                        <span>Настройки профиля</span>
                                    </a>
                                </li>
                                <li>
                                    <div class="dropdown-divider my-1"></div>
                                </li>
                                <li>
                                    <div class="d-grid px-4 pt-2 pb-1">
                                        <form action="{{route('logout')}}" method="post">
                                            @csrf
                                            @method('post')
                                            <button type="submit" class="btn btn-danger d-flex">
                                                <small class="align-middle">Выйти</small>
                                                <i class="ri ri-logout-box-r-line ms-2 ri-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!--/ User -->
                    </ul>
                </div>
            </nav>

            <!-- / Navbar -->

            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    @yield('content')
                </div>
                <!-- / Content -->


                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->

<script src="{{asset('assets/vendor/libs/jquery/jquery.js')}}"></script>

<script src="{{asset('assets/vendor/libs/popper/popper.js')}}"></script>
<script src="{{asset('assets/vendor/js/bootstrap.js')}}"></script>
<script src="{{asset('assets/vendor/libs/node-waves/node-waves.js')}}"></script>

<script src="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>

<script src="{{asset('assets/vendor/js/menu.js')}}"></script>

<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>

<!-- Main JS -->

<script src="{{asset('assets/js/main.js')}}"></script>

<!-- Page JS -->
<script src="{{asset('assets/js/dashboards-analytics.js')}}"></script>

<!-- Place this tag before closing body tag for github widget button. -->
<script async="async" defer="defer" src="https://buttons.github.io/buttons.js"></script>



@stack('scripts')
</body>
</html>
