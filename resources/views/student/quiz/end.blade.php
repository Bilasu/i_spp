@extends('student.layout')
@section('customCss')
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="lugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection

@section('content')
    <div class="content-wrapper">

        {{-- Breadcrumb for Quiz Result Page --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1>Keputusan Kuiz</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right bg-transparent">
                            <li class="breadcrumb-item">
                                <a href="{{ route('student.dashboard') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('student.quizcategory.read') }}">Back</a>
                            </li>
                            <li class="breadcrumb-item active">Quiz Result</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-8">

                        {{-- Flash Messages --}}
                        @if (Session::has('success'))
                            <div class="alert alert-success">
                                {{ Session::get('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Quiz Result Card --}}
                        <div class="card shadow-lg mt-5">
                            <div class="card-body text-center" style="padding-bottom: 100px;">

                                <h2 class="mb-4" style="color: #333;">📊 Quiz Result</h2>

                                {{-- Current Result --}}
                                <div class="bg-light p-4 rounded mb-4">
                                    <h4 class="text-primary">🎯 Current Quiz Result</h4>

                                    @php
                                        $scorePercent = round(($latestResult->correct / $latestResult->total) * 100);
                                    @endphp

                                    <div class="mt-3 text-left">
                                        <p><strong>✅ Correct:</strong> {{ $latestResult->correct }}</p>
                                        <p><strong>❌ Wrong:</strong> {{ $latestResult->wrong }}</p>
                                        <p><strong>📋 Total Questions:</strong> {{ $latestResult->total }}</p>
                                        <p><strong>🕒 Date:</strong>
                                            {{ \Carbon\Carbon::parse($latestResult->taken_at)->format('d M Y, h:i A') }}</p>
                                        <p><strong>📈 Score:</strong> {{ $scorePercent }}%</p>

                                        @if ($scorePercent == 100)
                                            <p class="text-success font-weight-bold">🌟 Perfect Score! Well done!</p>
                                        @elseif ($scorePercent < 50)
                                            <p class="text-danger font-weight-bold">⚠️ You can improve next time. Keep
                                                practicing!</p>
                                        @endif
                                    </div>

                                    {{-- Progress Bar --}}
                                    <div class="mt-3">
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $scorePercent }}%"></div>
                                        </div>
                                        <small class="text-muted">Score: {{ $scorePercent }}%</small>
                                    </div>
                                </div>

                                {{-- Previous Attempts --}}
                                <h4 class="mt-5 mb-3 text-secondary">📘 Previous Attempts</h4>

                                @if ($previousResults->isEmpty())
                                    <p class="text-muted">No previous attempts found.</p>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th class="text-center">Correct</th>
                                                    <th class="text-center">Wrong</th>
                                                    <th class="text-center">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($previousResults as $result)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($result->taken_at)->format('d M Y') }}
                                                        </td>
                                                        <td class="text-center text-success">{{ $result->correct }}</td>
                                                        <td class="text-center text-danger">{{ $result->wrong }}</td>
                                                        <td class="text-center">{{ $result->total }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </section>
    </div>
@endsection

@section('customJs')
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="Plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

    <script src="dist/js/adminlte.min2167.js?v=3.2.0"></script>

    <script src="dist/js/demo.js"></script>

    <script>
        $(function() {
            let table = $("#example1").DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],


                dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
                language: {
                    search: '',
                    searchPlaceholder: "🔍 Search teachers..."
                }
            });

            table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

            // Add custom styling to the search box
            $('#example1_filter input')
                .addClass('form-control form-control-sm rounded-pill ml-2')
                .css({
                    width: '200px',
                    display: 'inline-block'
                });

            // Move buttons to custom container
            table.buttons().container().appendTo('#datatable-buttons');
        });
    </script>
@endsection
