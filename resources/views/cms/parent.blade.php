<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> {{ env('APP_NAME') }} | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('cms/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet"
        href="{{ asset('cms/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('cms/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('cms/plugins/jqvmap/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('cms/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('cms/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('cms/plugins/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('cms/plugins/summernote/summernote-bs4.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.2.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('cms/dist/css/custom.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('cms/plugins/toastr/toastr.min.css') }}"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="{{ asset('cms/dist/img/photo_5197535986307950046_x__1_-removebg-preview.png') }}" rel="icon">
    @yield('style')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('dashpard') }}" class="nav-link">الصفحة الرئيسية</a>
                </li>
            </ul>

        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('dashpard') }}" class="brand-link">
                <img src="{{ asset('cms/dist/img/photo_5197535986307950046_x__1_-removebg-preview.png') }}"
                    class="brand-image img-circle elevation-3">

                <span class="brand-text font-weight-light">{{ env('APP_NAME') }}</span>
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="{{ auth()->user()->image_profile ?? asset('cms/dist/img/user2-160x160.jpg') }}"
                            class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ auth()->user()->name ?? '' }}</a>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        {{-- <li class="nav-header">الموارد البشرية</li> --}}

                        <li class="nav-item has-treeview {{ request()->routeIs('admins.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('admins.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-tie"></i>

                                <p>
                                    المشروفين
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview"
                                style="display: {{ request()->routeIs('admins.*') ? 'block' : 'none' }};">
                                <li class="nav-item">
                                    <a href="{{ route('admins.index') }}"
                                        class="nav-link {{ request()->routeIs('admins.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>عرض</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admins.create') }}"
                                        class="nav-link {{ request()->routeIs('admins.create') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>إنشاء</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item has-treeview {{ request()->routeIs('accounts.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                                <i class="nav-icon ion ion-person-stalker"></i>
                                <p>
                                    المستخدمين
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview"
                                style="display: {{ request()->routeIs('accounts.*') ? 'block' : 'none' }};">
                                <li class="nav-item">
                                    <a href="{{ route('accounts.TajirIndex') }}"
                                        class="nav-link {{ request()->routeIs('accounts.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>عرض التجار</p>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav nav-treeview"
                                style="display: {{ request()->routeIs('accounts.*') ? 'block' : 'none' }};">
                                <li class="nav-item">
                                    <a href="{{ route('accounts.MazarieIndex') }}"
                                        class="nav-link {{ request()->routeIs('accounts.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>عرض المزارعين</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{--  --}}
                        <li class="nav-header">إدارة المحتوى</li>

                        <li class="nav-item has-treeview {{ request()->routeIs('countries.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('countries.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-city"></i>
                                <p>
                                    المدن
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview"
                                style="display: {{ request()->routeIs('countries.*') ? 'block' : 'none' }};">
                                <li class="nav-item">
                                    <a href="{{ route('countries.index') }}"
                                        class="nav-link {{ request()->routeIs('countries.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>عرض</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('countries.create') }}"
                                        class="nav-link {{ request()->routeIs('countries.create') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>إنشاء</p>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li class="nav-item has-treeview {{ request()->routeIs('products.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>
                                    التصنيفات
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview"
                                style="display: {{ request()->routeIs('products.*') ? 'block' : 'none' }};">
                                <li class="nav-item">
                                    <a href="{{ route('products.index') }}"
                                        class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>عرض</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('products.create') }}"
                                        class="nav-link {{ request()->routeIs('products.create') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>إنشاء</p>
                                    </a>
                                </li>
                            </ul>
                        </li>



                        <li class="nav-item has-treeview {{ request()->routeIs('auctions.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('auctions.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>
                                    المزادات
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview"
                                style="display: {{ request()->routeIs('auctions.*') ? 'block' : 'none' }};">
                                <li class="nav-item">
                                    <a href="{{ route('auctions.index') }}"
                                        class="nav-link {{ request()->routeIs('auctions.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>عرض</p>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li class="nav-header">بيانات النظام</li>

                        <li class="nav-item">
                            <a href="{{ route('contactUs.index') }}" class="nav-link">
                                <i class="nav-icon ion ion-chatbubble"></i>
                                <p>تواصل معنا</p>
                            </a>
                        </li>

                        <li class="nav-item has-treeview {{ request()->routeIs('conditions.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('conditions.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-pen"></i>
                                <p>
                                    الشروط والاحكام
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview"
                                style="display: {{ request()->routeIs('conditions.*') ? 'block' : 'none' }};">
                                <li class="nav-item">
                                    <a href="{{ route('conditions.index') }}"
                                        class="nav-link {{ request()->routeIs('conditions.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>عرض</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('conditions.create') }}"
                                        class="nav-link {{ request()->routeIs('conditions.create') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>إنشاء</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li
                            class="nav-item has-treeview {{ request()->routeIs('notifications.*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                                <i class="nav-icon far fa-bell"></i>
                                <p>
                                    الاشعارت
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview"
                                style="display: {{ request()->routeIs('notifications.*') ? 'block' : 'none' }};">
                                <li class="nav-item">
                                    <a href="{{ route('notifications.index') }}"
                                        class="nav-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>عرض</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('notifications.create') }}"
                                        class="nav-link {{ request()->routeIs('notifications.create') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>إنشاء</p>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        {{-- <li class="nav-header">الاعدادات</li> --}}
                        <li class="nav-item">
                            <a href="{{ route('logout') }}" class="nav-link">
                                <i class="fas fa-sign-out-alt"></i>
                                <p>تسجيل الخروج</p>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">@yield('title')</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content -->
            @yield('main-content')
            <!-- /.content -->
        </div>
        <footer class="main-footer">
            <strong>حقوق الطبع والنشر &copy; 2024-2025 <a
                    href="{{ route('dashpard') }}">{{ env('APP_NAME') }}</a>.</strong>
            جميع الحقوق محفوظة.
            <div class="float-right d-none d-sm-inline-block">
                <b>إصدار</b> 2024
            </div>
        </footer>

        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>

    <script src="{{ asset('cms/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('cms/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <script src="https://cdn.rtlcss.com/bootstrap/v4.2.1/js/bootstrap.min.js"></script>
    <script src="{{ asset('cms/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('cms/plugins/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('cms/plugins/sparklines/sparkline.js') }}"></script>
    <script src="{{ asset('cms/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('cms/plugins/jqvmap/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('cms/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <script src="{{ asset('cms/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('cms/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('cms/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('cms/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('cms/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('cms/dist/js/adminlte.js') }}"></script>
    <script src="{{ asset('cms/dist/js/pages/dashboard.js') }}"></script>
    <script src="{{ asset('cms/dist/js/demo.js') }}"></script>
    <script src="https://unpkg.com/axios@0.27.2/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- <script src="{{ asset('cms/plugins/sweetalert2/sweetalert2.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('cms/plugins/toastr/toastr.min.js') }}"></script> --}}




    @yield('script')
</body>

</html>
