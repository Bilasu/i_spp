@extends('student.layout')

@section('customCss')
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <style>
        /* Style for the whole class info box */
        .class-info-box {
            background-color: white;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        /* Style for the class name */
        .class-name {
            font-size: 1.25em;
            font-weight: bold;
        }

        /* Style for the teacher info */
        .teacher-info {
            font-size: 1em;
            margin-top: 10px;
        }

        /* Style for the list of teachers */
        .teacher-list {
            display: inline-block;
        }

        /* Individual teacher name */
        .teacher-name {
            padding: 5px 10px;
            background-color: #f0f0f0;
            border-radius: 3px;
            margin-right: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>My Class</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">My Classroom</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">

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
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Classrooms Section -->
                <div class="row">
                    {{-- <h2 class="mb-4">Kelas Saya</h2> --}}

                    @foreach ($classrooms as $classroom)
                        <div class="col-12 card mb-3">
                            <div class="card-body">
                                <div class="class-info-box"
                                    style="border: 1px solid rgba(0, 0, 0, 0.05); box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1); padding: 15px; border-radius: 8px;">

                                    <!-- Dalam body -->
                                    <h5 class="class-name"
                                        style="font-family: 'Montserrat', sans-serif; font-size: 24px; font-weight: 600; color: #1e88e5;">
                                        {{ $classroom->class_name }}
                                    </h5>
                                    <p class="teacher-info">
                                        <strong
                                            style="font-family: 'Montserrat', sans-serif; font-size: 18px; color: #000000;">Guru:</strong>
                                    <div class="teacher-list">
                                        @foreach ($classroom->teachers as $teacher)
                                            <span class="teacher-name">{{ $teacher->name }}</span>
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </div>
                                    </p>
                                </div>

                                <!-- Display Assignments -->
                                @if ($classroom->assignments->count())
                                    <h6 class="mt-3">Senarai Tugasan:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped assignment-table"
                                            id="assignment-table-{{ $classroom->id }}"
                                            style="table-layout: fixed; width: 100%;">
                                            <thead style="background-color: #1e88e5; color: white;"
                                                class="table table-bordered">
                                                <tr>
                                                    <th>Tittle</th>
                                                    <th>Description</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th> Remaining Time</th>
                                                    <th>Acion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($classroom->assignments as $assignment)
                                                    <tr>
                                                        <td>{{ $assignment->title }}</td>
                                                        <td>{{ $assignment->description }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($assignment->start_date)->format('d/m/Y') }}
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($assignment->due_date)->format('d/m/Y') }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $now = \Carbon\Carbon::now();
                                                                $end = \Carbon\Carbon::parse($assignment->due_date);
                                                            @endphp
                                                            @if ($now->lt($end))
                                                                <p>Still Remaining
                                                                    {{ $now->diffForHumans($end, ['parts' => 2, 'join' => true, 'syntax' => 1]) }}
                                                                    before deadline</p>
                                                            @else
                                                                <p>Assignment due already end</p>
                                                                <span class="badge badge-danger">Already Due</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('student.submission.index', $assignment->id) }}"
                                                                class="btn btn-sm btn-primary">
                                                                Submit
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot style="background-color: #1e88e5; color: white;">
                                                <th>Tittle</th>
                                                <th>Description</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Remaining Time</th>
                                                <th>Acion</th>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">No aasignment yet for this class</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
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
                    searchPlaceholder: "🔍 Search assignment..."
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
