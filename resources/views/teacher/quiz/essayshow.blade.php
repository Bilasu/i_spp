@extends('teacher.layout')

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
                        <h1>List of Essay Question</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb  float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item">
                                <a
                                    href="{{ route('teacher.essay.read', ['quiz_category_id' => $question->quiz_category_id]) }}">Back</a>
                            </li>


                            <li class="breadcrumb-item active">List of Essay Question</li>
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

                            {{-- Paparkan Soalan --}}
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    @php
                                        $actualQuestion = $question->question;
                                        $markTotal = $question->mark_total ?? '—';
                                    @endphp

                                    <h5 class="mb-0">
                                        Question: {{ $actualQuestion }}
                                    </h5>
                                </div>

                                <div class="card-body">
                                    @forelse($question->answers as $answer)
                                        <div class="border rounded p-3 mb-4">
                                            <p><strong>Student:</strong> {{ $answer->student->name }}</p>
                                            <p><strong>Answer:</strong> {{ $answer->answer }}</p>

                                            @if ($isOwner)
                                                @if ($answer->mark !== null && $answer->comment)
                                                    <div class="alert alert-success">
                                                        <p><strong>Mark:</strong> {{ $answer->mark }} /
                                                            {{ $question->mark_total }}</p>
                                                        <p><strong>Comment:</strong> {{ $answer->comment }}</p>
                                                    </div>
                                                @else
                                                    <form action="{{ route('teacher.essay.mark', $answer->id) }}"
                                                        method="POST">
                                                        @csrf

                                                        <input type="hidden" name="mark_total"
                                                            value="{{ $question->mark_total }}">

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label>Mark Obtained:</label>
                                                                <input type="number" name="mark_obtained"
                                                                    class="form-control @error('mark_obtained') is-invalid @enderror"
                                                                    value="{{ old('mark_obtained', $answer->mark) }}"
                                                                    min="0" max="{{ $question->mark_total }}"
                                                                    required>
                                                                @error('mark_obtained')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label>Full Mark:</label>
                                                                <input type="number" class="form-control"
                                                                    value="{{ $question->mark_total }}" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="form-group mt-3">
                                                            <label>Comment:</label>
                                                            <input type="text" name="comments" class="form-control"
                                                                value="{{ old('comments', $answer->comment) }}">
                                                        </div>

                                                        <button type="submit" class="btn btn-sm btn-success mt-2">
                                                            💾 Save Comment and Mark
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <div class="alert alert-secondary">
                                                    @if ($answer->mark !== null)
                                                        <p><strong>Mark:</strong> {{ $answer->mark }} /
                                                            {{ $question->mark_total }}</p>
                                                    @else
                                                        <p><strong>Mark:</strong> Not graded</p>
                                                    @endif
                                                    <p><strong>Comment:</strong> {{ $answer->comment ?? 'Tiada komen' }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="alert alert-info">No answer for this question.</div>
                                    @endforelse
                                </div>
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
                    searchPlaceholder: "🔍 Find student..."
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
