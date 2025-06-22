@extends('student.layout')

@section('customCss')
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid py-4">
                <div class="row justify-content-center">
                    <div class="col-md-10">

                        <!-- Success & Error Alert -->
                        @if (Session::has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ Session::get('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Quiz Card -->
                        <div class="card shadow-lg rounded-4 border-0">
                            <div class="card-body p-5 bg-light">

                                <!-- Header Info -->
                                <div class="mb-4">
                                    <h4 class="fw-bold text-primary mb-2">
                                        <i class="fas fa-trophy me-2"></i> Quiz Category:
                                        <span
                                            class="text-dark">{{ $quiz_category->name ?? 'Kategori tidak ditemui' }}</span>
                                    </h4>

                                    @if (!isset($no_questions) || !$no_questions)
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ isset($currentIndex) && $totalQuestions > 0 ? round((($currentIndex + 1) / $totalQuestions) * 100) : 0 }}%"
                                                aria-valuenow="{{ isset($currentIndex) ? $currentIndex + 1 : 0 }}"
                                                aria-valuemin="0" aria-valuemax="{{ $totalQuestions ?? 0 }}">
                                            </div>
                                        </div>
                                    @endif

                                    <small class="text-muted">
                                        Question {{ ($currentIndex ?? 0) + 1 }} over {{ $totalQuestions ?? '?' }}
                                    </small>
                                </div>


                                @if (isset($no_questions) && $no_questions)
                                    <div class="text-center py-5">
                                        <h5 class="text-muted">No question for this category</h5>
                                    </div>
                                @else
                                    @if (isset($question) && is_object($question))
                                        <form method="POST" action="{{ route('student.quiz.submit') }}">
                                            @csrf

                                            <!-- Soalan -->
                                            <h5 class="fw-semibold mb-4 text-dark">
                                                <i class="fas fa-question-circle me-2 text-secondary"></i>
                                                {{ $question->question }}
                                            </h5>

                                            <!-- Pilihan Jawapan -->
                                            @foreach (['a', 'b', 'c', 'd'] as $option)
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="radio" name="ans"
                                                        id="ans_{{ $option }}" value="{{ $option }}"
                                                        {{ isset($selectedAnswer) && $selectedAnswer === $option ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="ans_{{ $option }}">
                                                        {{ $question->$option }}
                                                    </label>
                                                </div>
                                            @endforeach


                                            <!-- Hidden Fields -->
                                            <input type="hidden" name="dbnans" value="{{ $question->ans }}">
                                            <input type="hidden" name="question_id" value="{{ $question->id }}">
                                            <input type="hidden" name="current_index" value="{{ $currentIndex ?? 0 }}">

                                            <!-- Navigation Buttons -->
                                            <div class="d-flex justify-content-between align-items-center mt-5">
                                                @if (($currentIndex ?? 0) > 0)
                                                    <a href="{{ route('student.quiz.back', ['index' => $currentIndex - 1]) }}"
                                                        class="btn btn-outline-secondary rounded-pill px-4">
                                                        <i class="fas fa-arrow-left me-1"></i> Back
                                                    </a>
                                                @else
                                                    <div></div>
                                                @endif

                                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                                    Seterusnya <i class="fas fa-arrow-right ms-1"></i>
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <h5 class="text-center text-danger">No question found</h5>
                                    @endif
                                @endif

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
