<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teacher Dashboard</title>
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
    @yield('customCss')
    {{-- <style>
        /* Ensure the content has enough margin to not overlap footer */
        .content-wrapper {
            margin-bottom: 100px;
            /* adjust this value based on your footer height */
        }

        /* Ensure footer is always at the bottom */
        .main-footer {
            position: relative;
            bottom: 0;
            width: 100%;
            padding: 10px 0;
        }
    </style> --}}
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
                    <h3>Teacher Panel</h3>
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

                        <li class="nav-item menu-open">
                            <a href="{{ route('teacher.dashboard') }}" class="nav-link active">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                        </li>
                        {{-- Notes Management --}}
                        <li class="nav-item">
                            <a href="{{ route('teacher.notes.read') }}" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>
                                    Notes Management

                                </p>
                            </a>


                        </li>












                        {{-- Pembelajaran & Pengajaran (PdP) --}}
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-school"></i>
                                <p>
                                    Teaching and Learning (T&L)
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('teacher.classrooms.index') }}" class="nav-link">
                                        <i class="nav-icon fas fa-chalkboard"></i>
                                        <p>My Classes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('teacher.exams.index') }}" class="nav-link">
                                        <i class="nav-icon fas fa-clipboard-check"></i>
                                        <p>Examination Records</p>
                                    </a>
                                </li>

                            </ul>

                        </li>



                        {{-- Examination Records --}}

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>
                                    Quiz Management
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('teacher.quizcategory.read') }}" class="nav-link">
                                        <i class="nav-icon fas fa-pencil-alt"></i>

                                        <p>Quiz Category Management</p>
                                    </a>
                                </li>

                            </ul>

                        </li>

                        {{-- User Profile --}}
                        <li class="nav-item">
                            <a href="{{ route('teacher.logout') }}" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>
                                    Logout

                                </p>
                            </a>
                    </ul>
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
                {{-- @yield('scripts') --}}
        </footer>
        {{-- @yield('scripts') --}}
        <aside class="control-sidebar control-sidebar-dark">

        </aside>

    </div>


    <!-- Load jQuery first -->
    <script src="plugins/jquery/jquery.min.js"></script>

    <!-- Load jQuery UI (if needed for specific functionality) -->
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>

    <!-- Load Bootstrap (depends on jQuery) -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Load plugins that depend on jQuery -->
    <script src="plugins/sparklines/sparkline.js"></script>
    <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>

    <!-- Load other plugins -->
    <script src="plugins/chart.js/Chart.min.js"></script>
    <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>

    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="plugins/summernote/summernote-bs4.min.js"></script>
    <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

    <!-- Load AdminLTE -->
    <script src="dist/js/adminlte2167.js?v=3.2.0"></script>

    <!-- Load custom demo and dashboard scripts -->
    <script src="dist/js/demo.js"></script>
    <script src="dist/js/pages/dashboard.js"></script>

    @yield('customJs') <!-- Custom JavaScript for specific pages -->

</body>


</html>
