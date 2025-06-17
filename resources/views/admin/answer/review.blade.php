@extends('admin.layout')

@section('customCss')
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Semakan Jawapan Pelajar</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Semakan Jawapan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card p-3">
                    <h4 class="mb-4">Kategori: <span class="text-primary">{{ $category->name }}</span></h4>

                    @forelse ($category->questions as $question)
                        <div class="card mb-4 border-left-primary shadow">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Soalan: {{ $question->question }}</h5>
                            </div>
                            <div class="card-body">
                                @forelse ($question->answers as $answer)
                                    <div class="border rounded p-3 mb-3 bg-light">
                                        <p><strong>Pelajar:</strong> {{ $answer->student->name }}</p>
                                        <p><strong>Jawapan:</strong> {{ $answer->answer }}</p>
                                        <p>
                                            <strong>Markah:</strong>
                                            @if ($answer->marks !== null)
                                                <span class="badge badge-success">{{ $answer->marks }}/10</span>
                                            @else
                                                <span class="badge badge-secondary">Belum dinilai</span>
                                            @endif
                                        </p>
                                        <p>
                                            <strong>Komen:</strong><br>
                                            {!! $answer->comment ? nl2br(e($answer->comment)) : '<span class="text-muted">Tiada komen</span>' !!}
                                        </p>
                                    </div>
                                @empty
                                    <div class="alert alert-warning">Tiada jawapan dihantar untuk soalan ini.</div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-danger">Tiada soalan ditemui untuk kategori ini.</div>
                    @endforelse
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
                    searchPlaceholder: "🔍 Cari soalan kuiz..."
                }
            });

            table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example1_filter input')
                .addClass('form-control form-control-sm rounded-pill ml-2')
                .css({
                    width: '200px',
                    display: 'inline-block'
                });
        });
    </script>
@endsection
