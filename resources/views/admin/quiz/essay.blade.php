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
                        <h1>Senarai Soalan Essay</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.quizcategory.read') }}">Back</a></li>
                            <li class="breadcrumb-item active">List of Essay Questions</li>
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

                            <div class="card-header">
                                <h3 class="card-title">List of Essay Questions</h3>

                            </div>

                            <div class="card-body">
                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#addQuestionModal">
                                    <i class="fas fa-plus"></i> Add Question
                                </button>
                                <table id="example1" class="table table-bordered table-striped"
                                    style="table-layout: fixed; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Question</th>
                                            <th>Total Mark</th>
                                            <th>Review Answer</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($questions as $question)
                                            <tr>
                                                <td>{{ $question->question }}</td>
                                                <td>{{ $question->mark_total }}</td>
                                                <td>
                                                    <a href="{{ route('admin.quiz.essayshow', [$question->category->id, $question->id]) }}"
                                                        class="btn btn-primary btn-sm">
                                                        Review Answer
                                                    </a>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#editQuestionModal{{ $question->id }}">
                                                        Edit
                                                    </button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger" data-toggle="modal"
                                                        data-target="#deleteQuestionModal{{ $question->id }}">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Modal Edit Soalan -->
                                            <div class="modal fade" id="editQuestionModal{{ $question->id }}" tabindex="-1"
                                                aria-labelledby="editQuestionModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form
                                                            action="{{ route('admin.subjective.update', $question->id) }}"
                                                            method="POST">
                                                            @csrf

                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editQuestionModalLabel">Edit
                                                                    Soalan</h5>
                                                                <input type="hidden" name="quiz_category_id"
                                                                    value="{{ $question->quiz_category_id }}">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>

                                                            @php
                                                                $actualQuestion = $question->question;
                                                                $totalMark = $question->mark_total;
                                                            @endphp

                                                            <div class="modal-body">
                                                                {{-- Input soalan --}}
                                                                <div class="form-group">
                                                                    <label for="question">Question</label>
                                                                    <input type="text" name="question" id="question"
                                                                        class="form-control" value="{{ $actualQuestion }}"
                                                                        required>
                                                                </div>

                                                                {{-- Input markah penuh --}}
                                                                <div class="form-group">
                                                                    <label for="mark_total">Full Mark</label>
                                                                    <input type="number" name="mark_total" id="mark_total"
                                                                        class="form-control" value="{{ $totalMark }}"
                                                                        min="5" max="100" required>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>



                                            <!-- Modal Padam Soalan -->
                                            <div class="modal fade" id="deleteQuestionModal{{ $question->id }}"
                                                tabindex="-1" aria-labelledby="deleteQuestionModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.essay.delete', $question->id) }}"
                                                            method="GET">
                                                            @csrf
                                                            {{-- @method('DELETE') --}}
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteQuestionModalLabel">
                                                                    Delete
                                                                    Question</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are u sure to delete this question?</p>
                                                                <p><strong>{{ $question->question }}</strong></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">Yes,
                                                                    Delete</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Tambah Soalan -->
            <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.essay.store') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addQuestionModalLabel">Add New Question </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="question">Question</label>
                                    <input type="hidden" name="quiz_category_id" value="{{ $quiz_category_id }}">
                                    <input type="text" name="question" id="question" class="form-control"
                                        placeholder="Masukkan soalan di sini" required>
                                </div>
                                <div class="form-group">
                                    <label for="mark_total">Total Mark</label>
                                    <input type="number" name="mark_total" id="mark_total" class="form-control"
                                        placeholder="Example: 10" min="5" max="100" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Question</button>
                            </div>
                        </form>
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
                    searchPlaceholder: "🔍 Find essay question..."
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
