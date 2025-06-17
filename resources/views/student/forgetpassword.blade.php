<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from adminlte.io/themes/v3/pages/examples/login-v2.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 06 May 2024 05:17:02 GMT -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student | Forgot Password</title>
    <base href="{{ asset('admincss') }}/" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback">

    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">

    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">

    <link rel="stylesheet" href="dist/css/adminlte.min2167.css?v=3.2.0">

</head>

<body class="hold-transition login-page">
    <div class="login-box">

        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                {{-- @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error') }}</div>
                @endif --}}
                <a class="h1"><b>Student | Forgot Password</a>
            </div>
            <div class="card-body">
                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error') }}</div>
                @endif
                {{-- <pre>{{ print_r(session()->all(), true) }}</pre> --}}
                <p class="login-box-msg">Enter your identification card number here</p>
                {{-- @php
                    dd(session()->all());
                @endphp --}}
                <form action="{{ route('student.emailforgot') }}" method="POST">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="ic" class="form-control" placeholder="Enter your IC number"
                            required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    @error('ic')
                        <p class="text-danger">{{ $message }}</p>
                    @enderror

                    <div class="row">
                        <div class="col-8"></div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block">Send Reset Email</button>
                        </div>
                    </div>
                </form>



            </div>

        </div>

    </div>


    <script src="plugins/jquery/jquery.min.js"></script>

    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="dist/js/adminlte.min2167.js?v=3.2.0"></script>
</body>


</html>
