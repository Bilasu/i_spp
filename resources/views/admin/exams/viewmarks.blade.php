@extends('admin.layout')

@section('customCss')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">


    <style>
        .dt-buttons {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 15px;
        }

        .dt-button {
            margin-left: 10px;
        }

        #example1_filter {
            flex: 1;
            text-align: right;
        }

        #example1_filter input {
            width: 200px;
            display: inline-block;
            margin-left: 10px;
        }

        .content-wrapper {
            padding-top: 20px;
        }

        .dt-buttons .dt-button {
            margin-right: 10px;
            display: inline-block;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid mb-2">
                <div class="row">
                    <div class="col-md-6">
                        <h3><i class="fas fa-file-alt"></i> Examination Mark List</h3>
                    </div>
                    <div class="col-md-6 text-right">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.exams.read') }}">Kembali</a></li>
                                <li class="breadcrumb-item active"> Examination Mark List</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">
                            <strong>{{ $exam->name }}</strong>
                            <span class="text-muted">({{ $classroom->class_name }})</span>
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="example1">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Student IC</th>
                                        <th>Mark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($students as $student)
                                        @php
                                            $mark = $marks->firstWhere('student_ic', $student->ic);
                                        @endphp
                                        <tr class="{{ empty($mark->mark) ? 'table-warning' : '' }}">
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->ic }}</td>
                                            <td>{{ $mark->mark ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No list of students available.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('customJs')
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            let className = "{{ $classroom->class_name }}"; // ← ambil nama kelas dari Blade

            $('#example1').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        title: className
                    },
                    {
                        extend: 'csv',
                        filename: className
                    },
                    {
                        extend: 'excel',
                        filename: className
                    },
                    {
                        extend: 'pdf',
                        filename: className,
                        title: className
                    },
                    {
                        extend: 'print',
                        title: className
                    }
                ],
                language: {
                    search: '',
                    searchPlaceholder: "🔍 Find student..."
                }
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            // Initialize DataTable only if it's not already initialized
            if (!$.fn.dataTable.isDataTable('#example1')) {
                $("#example1").DataTable({
                    dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
                    ],
                    language: {
                        search: '',
                        searchPlaceholder: "🔍 Find student..."
                    }
                });
            }
        });
    </script>
@endsection
