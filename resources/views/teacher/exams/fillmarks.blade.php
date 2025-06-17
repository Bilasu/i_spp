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
            <div class="container-fluid mb-2">
                <div class="row">
                    <div class="col-md-6">
                        <h3><i class="fas fa-clipboard-list"></i> Isi Markah Peperiksaan</h3>
                    </div>
                    <div class="col-md-6 text-right">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Back</a></li>
                                <li class="breadcrumb-item active">List of Examination Marks</li>
                            </ol>
                        </nav>
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

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">
                            <strong>{{ $exam->name }}</strong>
                            <span class="text-muted">({{ $classroom->class_name }})</span>
                        </h5>

                        <p>
                            <strong>Tempoh Isi Markah:</strong>
                            {{ \Carbon\Carbon::parse($exam->start_date)->format('d/m/Y') }}
                            hingga
                            {{ \Carbon\Carbon::parse($exam->end_date)->format('d/m/Y') }}
                        </p>
                        <div id="datatable-buttons" class="mb-2"></div>

                        @if ($canFill)
                            <form action="{{ route('teacher.exams.storemarks', [$exam->id, $classroom->id]) }}"
                                method="POST">
                                @csrf
                                <div class="table-responsive">

                                    <table class="table table-bordered table-hover" id="example1">

                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Nama Pelajar</th>
                                                <th>No. IC</th>
                                                <th>Markah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($students as $student)
                                                @php
                                                    $mark = $marks->firstWhere('student_ic', $student->ic);
                                                @endphp
                                                <tr class="{{ empty($mark->mark) ? 'table-warning' : '' }}">
                                                    <td>{{ $student->name }}</td>
                                                    <td>{{ $student->ic }}</td>
                                                    <td>
                                                        <input type="number" class="form-control"
                                                            name="marks[{{ $student->ic }}]" min="0" max="100"
                                                            value="{{ old('marks.' . $student->ic, $mark->mark ?? '') }}"
                                                            placeholder="Masukkan markah" required>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-right mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Markah
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info mt-3">
                                <strong><i class="fas fa-clock"></i> Masa untuk isi markah telah tamat atau belum
                                    bermula.</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@php
    // Pastikan nama kelas selamat untuk dijadikan nama fail
    $safeClassName = isset($classroom)
        ? preg_replace('/[^A-Za-z0-9_\- ]/', '', $classroom->class_name)
        : 'Senarai_Pelajar';
@endphp
{{-- <code>{{ $safeClassName }}</code> --}}
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
            // Destroy dulu kalau dah ada DataTable, supaya tak duplicate
            if ($.fn.DataTable.isDataTable('#example1')) {
                $('#example1').DataTable().destroy();
            }

            let table = $("#example1").DataTable({
                dom: '<"d-flex justify-content-end align-items-center mb-2"B>r', // tanpa 'f' untuk search box
                responsive: true,
                searching: false, // hilangkan search box
                buttons: [{
                        extend: 'copy',
                        title: '{{ $safeClassName }}'
                    },
                    {
                        extend: 'csv',
                        title: '{{ $safeClassName }}'
                    },
                    {
                        extend: 'excel',
                        title: '{{ $safeClassName }}'
                    },
                    {
                        extend: 'pdf',
                        title: '{{ $safeClassName }}'
                    },
                    {
                        extend: 'print',
                        title: '{{ $safeClassName }}'
                    }
                ]
            });

            // Susun dan hias button
            table.buttons().container().appendTo('#datatable-buttons');
            $('.dt-button').addClass('custom-dt-btn');
            $('.dt-buttons').css({
                'display': 'flex',
                'justify-content': 'flex-end',
                'padding': '10px 0',
            });
            $('.dt-button').css({
                'background-color': '#007bff',
                'color': '#fff',
                'border': 'none',
                'border-radius': '4px',
                'padding': '5px 15px',
                'font-size': '14px',
                'margin-left': '10px',
                'cursor': 'pointer',
                'transition': 'background-color 0.3s ease, transform 0.3s ease',
            });
            $('.dt-button').hover(function() {
                $(this).css({
                    'background-color': '#0056b3',
                    'transform': 'scale(1.05)',
                    'box-shadow': '0 4px 8px rgba(0, 0, 0, 0.2)'
                });
            }, function() {
                $(this).css({
                    'background-color': '#007bff',
                    'transform': 'scale(1)',
                    'box-shadow': 'none'
                });
            });
        });
    </script>


    <style>
        .custom-dt-btn {
            background-color: #007bff !important;
            color: #fff !important;
            border: none !important;
            border-radius: 4px !important;
            padding: 5px 15px !important;
            font-size: 14px !important;
            margin-left: 10px !important;
            cursor: pointer !important;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .custom-dt-btn:hover {
            background-color: #0056b3 !important;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
@endsection
