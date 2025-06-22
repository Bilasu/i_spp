@extends('student.layout')

@section('customCss')
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1 class="text-primary"><i class="fas fa-poll"></i> All Results from Paper 1</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right bg-transparent">
                            <li class="breadcrumb-item">
                                <a href="{{ route('student.dashboard') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('student.quizcategory.read') }}">Back</a>
                            </li>
                            <li class="breadcrumb-item active">All Quiz Results </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content pb-5">
            <div class="container-fluid">
                @forelse ($resultsByCategory as $categoryName => $results)
                    <div class="card mb-4 shadow">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">📘 {{ $categoryName }}</h5>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-center mb-0"
                                    style="table-layout: fixed; width: 100%;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>✅ Correct</th>
                                            <th>❌ Wrong</th>
                                            <th>📋 Total Questions</th>
                                            <th>📈 Overall Score (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($results as $result)
                                            @php
                                                $score = round(($result->correct / $result->total) * 100);
                                            @endphp
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($result->taken_at)->format('d M Y, h:i A') }}
                                                </td>
                                                <td class="text-success">{{ $result->correct }}</td>
                                                <td class="text-danger">{{ $result->wrong }}</td>
                                                <td>{{ $result->total }}</td>
                                                <td>{{ $score }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info mt-3">
                        <strong><i class="fas fa-pen-alt"></i> No results found</strong>
                    </div>
                @endforelse
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
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
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
                    searchPlaceholder: "🔍 Search quiz..."
                }
            });

            table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example1_filter input')
                .addClass('form-control form-control-sm rounded-pill ml-2')
                .css({
                    width: '200px',
                    display: 'inline-block'
                });

            table.buttons().container().appendTo('#datatable-buttons');
        });
    </script>
@endsection
