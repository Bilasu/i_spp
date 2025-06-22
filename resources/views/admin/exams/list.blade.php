@extends('admin.layout')
@section('customCss')
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
@section('content')
    <div class="content-wrapper p-4">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Examination List</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Examination List</li>
                        </ol>
                    </div>
                </div>


            </div>
        </section>
        <div class="border p-3 mb-4 rounded">
            <a href="#" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addExamModal">Add Examination
            </a>
            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <table id="example1" class="table table-bordered table-striped" style="table-layout: fixed; width: 100%;">
                <thead>
                    <tr>
                        <th>Examination Name</th>
                        <th>Mark Entry Duration</th>
                        <th>Overall Status</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($exams as $exam)
                        @php
                            $statuses = [];
                        @endphp
                        @foreach ($exam->classrooms as $classroom)
                            @php
                                $total_students = $classroom->students->count();
                                $marks_filled = \App\Models\ExamMark::where('exam_id', $exam->id)
                                    ->where('classroom_id', $classroom->id)
                                    ->count();

                                $status = match (true) {
                                    $marks_filled == 0 => 'Not Started Yet',
                                    $marks_filled < $total_students => 'In Progress ',
                                    default => 'Completed',
                                };

                                // Set warna badge ikut status
                                $badge = match ($status) {
                                    'Not Started Yet' => 'badge-secondary',
                                    'In Progress' => 'badge-warning',
                                    'Completed' => 'badge-success',
                                };

                                // Generate URL ke page view markah
                                $url = route('admin.exams.viewMarks', [
                                    'exam_id' => $exam->id,
                                    'classroom_id' => $classroom->id,
                                ]);

                                // Simpan status lengkap dengan link
                                $statuses[] = "$classroom->class_name: <span class=\"badge $badge\">$status</span> <a href=\"$url\">($marks_filled/$total_students)</a>";
                            @endphp
                        @endforeach

                        <tr>
                            <td>{{ $exam->name }}</td>
                            <td>{{ $exam->start_date }} → {{ $exam->end_date }}</td>
                            {{-- <td>
                            @foreach ($exam->classrooms as $classroom)
                                <div>{{ $classroom->class_name }}</div>
                            @endforeach
                        </td> --}}
                            <td>
                                @foreach ($statuses as $s)
                                    <div>{!! $s !!}</div>
                                @endforeach
                            </td>
                            <td><a href="" class="btn btn-primary" data-toggle="modal"
                                    data-target="#editExamModal{{ $exam->id }}">Edit</a>
                            </td>

                            <td><a href="" class="btn btn-danger" data-toggle="modal"
                                    data-target="#deleteExamModal{{ $exam->id }}">Delete</a>
                            </td>

                        </tr>

                        <!-- Edit Exam Modal -->
                        @foreach ($exams as $exam)
                            <div class="modal fade" id="editExamModal{{ $exam->id }}" tabindex="-1"
                                aria-labelledby="editExamModalLabel{{ $exam->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form id="editExamForm{{ $exam->id }}" class="editExamForm"
                                            action="{{ route('admin.exams.update', $exam->id) }}" method="POST">
                                            @csrf
                                            {{-- @method('PUT') --}}
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Examination</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Examination Name</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ $exam->name }}" required>
                                                </div>

                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label>Date Start Fill Marks</label>
                                                        <input type="datetime-local" id="start_date_{{ $exam->id }}"
                                                            name="start_date" class="form-control"
                                                            value="{{ $exam->start_date->format('Y-m-d\TH:i') }}" required>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label>End Date Fill Marks</label>
                                                        <input type="datetime-local" id="end_date_{{ $exam->id }}"
                                                            name="end_date" class="form-control"
                                                            value="{{ $exam->end_date->format('Y-m-d\TH:i') }}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Choose Class</label>
                                                    <input type="text" id="searchClass_{{ $exam->id }}"
                                                        class="form-control mb-2" placeholder="Cari kelas...">
                                                    <button type="button" id="selectAllBtn_{{ $exam->id }}"
                                                        class="btn btn-sm btn-outline-primary mb-2">Choose All</button>
                                                    <div id="classCheckboxList_{{ $exam->id }}"
                                                        style="max-height: 200px; overflow-y: auto; border: 1px solid #ced4da; padding: 10px; border-radius: 5px;">
                                                        @foreach ($classrooms as $classroom)
                                                            <div class="form-check">
                                                                <input class="form-check-input classroom-checkbox"
                                                                    type="checkbox" name="classrooms[]"
                                                                    value="{{ $classroom->id }}"
                                                                    {{ in_array($classroom->id, $exam->classrooms->pluck('id')->toArray()) ? 'checked' : '' }}>
                                                                <label
                                                                    class="form-check-label">{{ $classroom->class_name }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach




                        <!-- Delete Exam Modal -->
                        <div class="modal fade" id="deleteExamModal{{ $exam->id }}" tabindex="-1"
                            aria-labelledby="deleteExamModalLabel{{ $exam->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.exams.delete', $exam->id) }}" method="GET">
                                        @csrf
                                        {{-- @method('DELETE') --}}

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteExamModalLabel{{ $exam->id }}">Delete
                                                Examination</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <p class="text-center">Are you sure to delete the examination?</p>
                                            <p class="text-center"><strong>{{ $exam->name }}</strong></p>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>

                        <th>Examination Name</th>
                        <th>Mark Entry Duration</th>
                        <th>Overall Status</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Add Exam Modal -->
    <div class="modal fade" id="addExamModal" tabindex="-1" aria-labelledby="addExamModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addExamForm" action="{{ route('admin.exams.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">AAdd Examination</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Examination Namen</label>
                            <input type="text" name="name" class="form-control"
                                placeholder="Masukkan Nama Peperiksaan" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Start Date Fill Marks</label>
                                <input type="datetime-local" id="add_start_date" name="start_date" class="form-control"
                                    required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>End Date Fill Marks</label>
                                <input type="datetime-local" id="add_end_date" name="end_date" class="form-control"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Choose Classs</label>
                            <input type="text" id="add_searchClass" class="form-control mb-2"
                                placeholder="Cari kelas...">
                            <button type="button" id="add_selectAllBtn"
                                class="btn btn-sm btn-outline-primary mb-2">Choose All</button>
                            <div id="add_classCheckboxList"
                                style="max-height: 200px; overflow-y: auto; border: 1px solid #ced4da; padding: 10px; border-radius: 5px;">
                                @foreach ($classrooms as $classroom)
                                    <div class="form-check">
                                        <input class="form-check-input classroom-checkbox" type="checkbox"
                                            name="classrooms[]" value="{{ $classroom->id }}">
                                        <label class="form-check-label">{{ $classroom->class_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Examination</button>
                    </div>
                </form>
            </div>
        </div>
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
                dom: '<"row mb-3"<"col-md-12"f><"col-md-4 text-end">>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row mt-2"<"col-md-6"i><"col-md-6"p>>', // Keeps the search box aligned left

                language: {
                    search: '',
                    searchPlaceholder: "🔍 Search examination..."
                }
            });

            // Styling the search box for custom width and alignment
            $('#example1_filter input')
                .addClass('form-control form-control-sm rounded-pill')
                .css({
                    width: '200px', // Adjust width as necessary
                    display: 'inline-block', // Keeps it inline with the button
                    marginRight: '10px' // Adds a small space from the button
                });

            // Optionally, move buttons to a custom container if needed
            // table.buttons().container().appendTo('#datatable-buttons');
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ========================
            // GLOBAL FUNCTION
            // ========================
            function initExamForm(form, examId = null) {
                const isEdit = !!examId;
                const prefix = isEdit ? `${examId}` : 'add';
                const startInput = document.getElementById(`${isEdit ? 'start_date_' + examId : 'add_start_date'}`);
                const endInput = document.getElementById(`${isEdit ? 'end_date_' + examId : 'add_end_date'}`);
                const selectAllBtn = document.getElementById(
                    `${isEdit ? 'selectAllBtn_' + examId : 'add_selectAllBtn'}`);
                const searchInput = document.getElementById(
                    `${isEdit ? 'searchClass_' + examId : 'add_searchClass'}`);
                const checkboxList = document.getElementById(
                    `${isEdit ? 'classCheckboxList_' + examId : 'add_classCheckboxList'}`);

                const checkboxes = checkboxList.querySelectorAll('input[type="checkbox"].classroom-checkbox');

                // Validate on submit
                form.addEventListener('submit', function(e) {
                    if (new Date(endInput.value) < new Date(startInput.value)) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'End Date is Invalid!',
                            text: 'End Date must be greater than Start Date.'
                        });
                    } else if (!Array.from(checkboxes).some(cb => cb.checked)) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Class Selected!',
                            text: 'Please select at least one class.'
                        });
                    }
                });

                // Select All
                if (selectAllBtn) {
                    selectAllBtn.addEventListener('click', function() {
                        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                        checkboxes.forEach(cb => cb.checked = !allChecked);
                        this.textContent = allChecked ? 'Select All' : 'Unselect All';
                    });
                }

                // Search filter
                if (searchInput) {
                    searchInput.addEventListener('keyup', function() {
                        const filter = this.value.toLowerCase();
                        checkboxList.querySelectorAll('.form-check').forEach(item => {
                            item.style.display = item.textContent.toLowerCase().includes(filter) ?
                                '' : 'none';
                        });
                    });
                }
            }

            // ========================
            // ADD Modal Init
            // ========================
            const addForm = document.getElementById('addExamForm');
            if (addForm) {
                initExamForm(addForm);
            }

            // ========================
            // Loop semua Edit Modals
            // ========================
            document.querySelectorAll('.editExamForm').forEach(form => {
                const examId = form.id.replace('editExamForm', '');
                initExamForm(form, examId);
            });
        });
    </script>
@endsection
