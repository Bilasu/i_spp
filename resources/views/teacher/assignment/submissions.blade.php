@extends('teacher.layout')
@section('customCss')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>List of Submitted Assignments: {{ $assignment->classroom->class_name }}</h1>

                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.classrooms.index') }}">Back</a></li>

                            <li class="breadcrumb-item active">List of Submitted Assignments: {{ $assignment->title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Table Section -->
                <div class="table-responsive mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-4">List of Submitted Assignments: {{ $assignment->title }}</h4>
                            <table class="table table-bordered table-hover" style="table-layout: fixed; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Student Namer</th>
                                        <th>File</th>
                                        <th>Date and Time Submitted</th>
                                        <th>Student Comment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Show students who have submitted --}}
                                    @foreach ($students as $student)
                                        @php
                                            $submission = $assignment->submissions
                                                ->where('student_ic', $student->ic)
                                                ->first();
                                        @endphp

                                        @if ($submission)
                                            <tr>
                                                <td>{{ $student->name }}</td>
                                                <td>
                                                    <a href="{{ asset('storage/' . $submission->file_path) }}" download
                                                        title="{{ basename($submission->file_path) }}">
                                                        {{ basename($submission->file_path) }}
                                                    </a>
                                                </td>
                                                <td
                                                    class="{{ \Carbon\Carbon::parse($submission->submitted_at)->gt($assignment->due_date) ? 'text-danger' : 'text-success' }}">
                                                    {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d/m/Y h:i A') }}
                                                </td>
                                                <td>{{ $submission->comment ?? '-' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    {{-- Show students who have not submitted --}}
                                    @foreach ($students as $student)
                                        @php
                                            $submission = $assignment->submissions
                                                ->where('student_ic', $student->ic)
                                                ->first();
                                        @endphp

                                        @if (!$submission)
                                            <tr>
                                                <td>{{ $student->name }}</td>
                                                <td colspan="3">Not submitted</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Student Namer</th>
                                        <th>File</th>
                                        <th>Date and Time Submitted</th>
                                        <th>Student Comment</th>
                                    </tr>
                                </tfoot>
                            </table>
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
                // responsive: true,
                // lengthChange: false,
                // autoWidth: false,
                // buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],


                dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
                language: {
                    search: '',
                    searchPlaceholder: "🔍 Search student..."
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
