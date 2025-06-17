@extends('student.layout')

@section('customCss')
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
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
                                <h5>{{ $classroom->class_name }}</h5>
                                <p><strong>Guru:</strong>
                                    @foreach ($classroom->teachers as $teacher)
                                        {{ $teacher->name }}@if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </p>

                                <!-- Display Assignments -->
                                @if ($classroom->assignments->count())
                                    <h6 class="mt-3">Senarai Tugasan:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped assignment-table"
                                            id="assignment-table-{{ $classroom->id }}">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Tajuk</th>
                                                    <th>Penerangan</th>
                                                    <th>Tarikh Mula</th>
                                                    <th>Tarikh Tamat</th>
                                                    <th>Baki Tempoh</th>
                                                    <th>Tindakan</th>
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
                                                                <p>Masih berbaki
                                                                    {{ $now->diffForHumans($end, ['parts' => 2, 'join' => true, 'syntax' => 1]) }}
                                                                    sebelum tarikh tamat.</p>
                                                            @else
                                                                <p>Tugasan telah tamat.</p>
                                                                <span class="badge badge-danger">Sudah Tamat</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('student.submission.index', $assignment->id) }}"
                                                                class="btn btn-sm btn-primary">
                                                                Jawab / Hantar
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">Tiada tugasan untuk kelas ini.</p>
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
                    searchPlaceholder: "🔍 Search notetypes..."
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
