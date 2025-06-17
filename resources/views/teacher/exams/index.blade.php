@extends('teacher.layout')
@section('customCss')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        #example1_wrapper .row {
            display: flex;
            justify-content: flex-end;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>List of Examination Marks</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">List of Examination Marks</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive mb-4">
                    {{-- Start of each exam --}}

                    @foreach ($exams as $exam)
                        <div class="card mb-4 p-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-body"
                                        style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; border-bottom: 2px solid #dee2e6;">
                                        <h3 class="mb-3" style="color: #2c3e50; font-weight: bold;">
                                            📄 {{ $exam->name }}
                                        </h3>

                                        <p style="font-size: 16px; color: #555;">
                                            <strong>🗓️ Tempoh Isi Markah:</strong><br>
                                            {{ \Carbon\Carbon::parse($exam->start_date)->format('d/m/Y') }}
                                            <span style="margin: 0 8px;">hingga</span>
                                            {{ \Carbon\Carbon::parse($exam->end_date)->format('d/m/Y') }}
                                        </p>


                                        @if ($exam->classrooms->count() > 0)
                                            <table border="1" cellpadding="8" cellspacing="0" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Kelas</th>
                                                        <th>Overall Status</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($exam->classrooms as $classroom)
                                                        @php
                                                            // Total students in the class
                                                            $students = $classroom->students;

                                                            // Total students in the class
                                                            $totalStudents = $students->count();

                                                            // Students who have marks for this exam
                                                            $markedStudents = $exam
                                                                ->examMarks()
                                                                ->where('classroom_id', $classroom->id)
                                                                ->whereIn('student_ic', $students->pluck('ic'))
                                                                ->count();

                                                            // Status message
                                                            $status =
                                                                $totalStudents == 0
                                                                    ? 'Tiada pelajar'
                                                                    : ($markedStudents >= $totalStudents
                                                                        ? 'Lengkap – ' .
                                                                            $markedStudents .
                                                                            '/' .
                                                                            $totalStudents
                                                                        : 'Belum Lengkap – ' .
                                                                            $markedStudents .
                                                                            '/' .
                                                                            $totalStudents);
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $classroom->class_name }}</td>
                                                            <td>{{ $status }}</td>
                                                            <td>
                                                                <a
                                                                    href="{{ route('teacher.exams.fillmarks', ['exam' => $exam->id, 'classroom' => $classroom->id]) }}">
                                                                    Fill Marks
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p>Tiada kelas untuk peperiksaan ini.</p>
                                        @endif



                                    </div>
                    @endforeach
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
                dom: '<"d-flex justify-content-between align-items-center mb-2"f>rtip',
                language: {
                    search: '',
                    searchPlaceholder: "🔍 Search notes..."
                }
            });

            // Move the search box to the right side
            $('#example1_filter').css('flex', '1').css('text-align', 'right'); // Position search box to the right

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
