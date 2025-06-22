@extends('teacher.layout')

@section('customCss')
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>List of Quiz Results</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.quizcategory.read') }}">Back</a></li>
                            <li class="breadcrumb-item active">List of Quiz Results</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
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



                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="classFilter"><strong>Filter by Class:</strong></label>
                                        <select id="classFilter" class="form-control">
                                            <option value="">-- All Class--</option>
                                            @foreach ($classList as $class)
                                                <option value="{{ $class->class_name }}">{{ $class->class_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <table id="example1" class="table table-bordered table-striped"
                                    style="table-layout: fixed; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Student IC</th>
                                            <th>Class</th> {{-- NEW --}}
                                            <th>Corect</th>
                                            <th>Wrong</th>
                                            <th>Total Questions</th>
                                            <th>Taken At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($quizResults as $result)
                                            <tr>
                                                <td><strong>{{ $result->user->name ?? 'Name not found' }}</strong></td>
                                                <td>{{ $result->user_ic }}</td>
                                                <td class="kelas-cell">
                                                    {{ $result->user->classrooms->first()->class_name ?? 'Not found' }}
                                                </td>
                                                <td><span class="badge badge-success">{{ $result->correct }}</span></td>
                                                <td><span class="badge badge-danger">{{ $result->wrong }}</span></td>
                                                <td><span class="badge badge-primary">{{ $result->total }}</span></td>
                                                <td>{{ $result->taken_at ? \Carbon\Carbon::parse($result->taken_at)->format('d/m/Y H:i') : 'N/A' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No student answer this
                                                    quiz</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Student IC</th>
                                            <th>Class</th> {{-- NEW --}}
                                            <th>Corect</th>
                                            <th>Wrong</th>
                                            <th>Total Questions</th>
                                            <th>Taken At</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div> <!-- /.card-body -->





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
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

    <script src="dist/js/teacherlte.min2167.js?v=3.2.0"></script>

    <script>
        $(function() {
            let table = $("#example1").DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"dt-buttons"><"dt-search"f>>rtip',
                language: {
                    search: '',
                    searchPlaceholder: "🔍 Cari student..."
                }
            });

            table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example1_filter input')
                .addClass('form-control form-control-sm rounded-pill ml-2')
                .css({
                    width: '200px',
                    display: 'inline-block'
                });

            // Connect existing dropdown filter
            $('#classFilter').on('change', function() {
                let selected = $(this).val();
                table.column(2).search(selected ? '^' + selected + '$' : '', true, false).draw();
            });
        });
    </script>
@endsection
