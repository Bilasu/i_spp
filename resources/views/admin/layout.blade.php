<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <base href="{{ asset('admincss') }}/" />

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback">

    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">

    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">

    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">

    <link rel="stylesheet" href="dist/css/adminlte.min2167.css?v=3.2.0">

    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">

    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">

    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @yield('customCss')

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        {{-- <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60"
                width="60">
        </div> --}}

        <nav class="main-header navbar navbar-expand navbar-white navbar-light">

            <ul class="navbar-nav">
                <div class="container-center"
                    style="display: flex; justify-content: center; align-items: center; height: 10vh;">
                    <h3>Admin Panel</h3>
                </div>

            </ul>


        </nav>


        <aside class="main-sidebar sidebar-dark-primary elevation-4">


            <div class="sidebar">

                <div class="user-panel mt-3 pb-3 mb-3 d-flex">

                    <div class="info">
                        <span style="color: white;">Menu</span>
                    </div>
                </div>



                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        <li class="nav-item menu-open" style="padding-top: 25px; padding-bottom: 15px;">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link active">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p style="font-size: 25px; margin-left: 30px;">
                                    Dashboard
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                        </li>

                        {{-- User Profile --}}
                        {{-- <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-chart-pie"></i>
                                <p>
                                    User Profile
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href={{ route('admin.logout') }} class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Logout</p>
                                    </a>
                                </li>

                            </ul>
                        </li> --}}
                        {{-- Student Management --}}
                        <li class="nav-item" style="padding-top: 25px; padding-bottom: 15px;">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p style="font-size: 25px; margin-left: 30px;">
                                    User Management
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview" style="padding-top: 25px; padding-bottom: 15px;">
                                <li class="nav-item">
                                    <a href={{ route('teacher.read') }} class="nav-link">
                                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                                        <p>Teacher Management</p>
                                    </a>
                                </li>
                                {{-- <li class="nav-item">
                                    <a href={{ route('teacher.read') }} class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>View Teacher List</p>
                                    </a>
                                </li> --}}

                            </ul>
                            <ul class="nav nav-treeview" style="padding-top: 25px; padding-bottom: 15px;">
                                <li class="nav-item">
                                    <a href={{ route('student.read') }} class="nav-link">
                                        <i class="nav-icon fas fa-user-graduate"></i>
                                        <p style="font-size: 25px; margin-left: 30px;">Student Management</p>
                                    </a>
                                </li>
                                {{-- <li class="nav-item">
                                    <a href={{ route('student.read') }} class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>View Student List</p>
                                    </a>
                                </li> --}}

                            </ul>
                        </li>


                        <li class="nav-item" style="padding-top: 25px; padding-bottom: 15px;">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p style="font-size: 25px; margin-left: 30px;">
                                    Notes Management
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" style="padding-top: 25px; padding-bottom: 15px;">
                                <li class="nav-item">
                                    <a href={{ route('notetypes.read') }} class="nav-link">
                                        <i class="nav-icon fas fa-tags"></i>
                                        <p style="font-size: 25px; margin-left: 30px;">Note Types Management</p>
                                    </a>
                                </li>
                                {{-- <li class="nav-item">
                                    <a href={{ route('notetypes.read') }} class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>View Note Types List</p>
                                    </a>
                                </li> --}}

                            </ul>
                            <ul class="nav nav-treeview" style="padding-top: 25px; padding-bottom: 15px;">
                                <li class="nav-item">
                                    <a href={{ route('admin.notes.read') }} class="nav-link">
                                        <i class="nav-icon fas fa-file-alt"></i>
                                        <p style="font-size: 25px; margin-left: 30px;">Note Management</p>
                                    </a>
                                </li>
                                {{-- <li class="nav-item">
                                    <a href={{ route('notes.read') }} class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>View Notes List</p>
                                    </a>
                                </li> --}}

                            </ul>
                        </li>






                        {{-- Pembelajaran & Pengajaran (PdP) --}}
                        <li class="nav-item" style="padding-top: 25px; padding-bottom: 15px;">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-school"></i>
                                <p style="font-size: 25px; margin-left: 30px;">
                                    Teaching and Learning (T&L)
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview" style="padding-top: 25px; padding-bottom: 15px;">
                                <li class="nav-item">
                                    <a href="{{ route('admin.classrooms.read') }}" class="nav-link">
                                        <i class="nav-icon fas fa-chalkboard"></i>
                                        <p style="font-size: 25px; margin-left: 30px;">Classes Management</p>
                                    </a>
                                </li>
                                <li class="nav-item" style="padding-top: 25px; padding-bottom: 15px;">
                                    <a href="{{ route('admin.exams.read') }}" class="nav-link">
                                        <i class="nav-icon fas fa-clipboard-check"></i>
                                        <p style="font-size: 25px; margin-left: 30px;">Examination Management</p>
                                    </a>
                                </li>

                            </ul>

                        </li>

                        {{-- Pembelajaran & Pengajaran (PdP) --}}
                        <li class="nav-item" style="padding-top: 25px; padding-bottom: 15px;">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p style="font-size: 25px; margin-left: 30px;">
                                    Quiz Management
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview" style="padding-top: 25px; padding-bottom: 15px;">
                                <li class="nav-item">
                                    <a href="{{ route('admin.quizcategory.read') }}" class="nav-link">
                                        <i class="nav-icon fas fa-pencil-alt"></i>

                                        <p style="font-size: 25px; margin-left: 30px;">Quiz Category Management</p>
                                    </a>
                                </li>

                            </ul>

                        </li>

                        {{-- User Profile --}}
                        <li class="nav-item" style="padding-top: 25px; padding-bottom: 15px;">
                            <a href="{{ route('admin.logout') }}" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p style="font-size: 25px; margin-left: 30px;">
                                    Logout

                                </p>
                            </a>
                            {{-- <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href={{ route('admin.logout') }} class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Logout</p>
                                    </a>
                                </li>

                            </ul> --}}
                        </li>
                </nav>

            </div>
        </aside>

        @yield('scripts')
        @yield('content')

        <footer class="main-footer">
            <strong>Sekolah Menengah Kebangsaan Dato' Mohd Yunos Sulaiman (SMKDYS)
                {{-- <div class="float-right d-none d-sm-inline-block">
                    <b>Version</b> 3.2.0
                </div> --}}
        </footer>

        <aside class="control-sidebar control-sidebar-dark">

        </aside>

    </div>


    <script src="plugins/jquery/jquery.min.js"></script>

    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>

    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>

    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="plugins/chart.js/Chart.min.js"></script>

    <script src="plugins/sparklines/sparkline.js"></script>

    <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>

    <script src="plugins/jquery-knob/jquery.knob.min.js"></script>

    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>

    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

    <script src="plugins/summernote/summernote-bs4.min.js"></script>

    <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

    <script src="dist/js/adminlte2167.js?v=3.2.0"></script>

    <script src="dist/js/demo.js"></script>

    <script src="dist/js/pages/dashboard.js"></script>
    @yield('customJs')
</body>


</html>
