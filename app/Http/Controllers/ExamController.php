<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Classroom;
use App\Models\ExamMark;

class ExamController extends Controller
{
    // Display list of all exams
    public function index()
    {
        $exams = Exam::with(['classrooms' => function ($query) {
            $query->where('status', 'active'); // Tapis kelas yang active sahaja
        }])->get();

        $classrooms = Classroom::where('status', 'active')->get(); // ambil semua kelas aktif untuk dropdown

        return view('admin.exams.list', compact('exams', 'classrooms'));
    }


    // Show form to create new exam
    // public function create()
    // {
    //     return view('admin.exams.create');
    // }

    // Store new exam and assign all classrooms
    public function store(Request $request)
    {
        // ✅ Validate input form
        $request->validate([
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'classrooms' => 'required|array', // Wajib pilih at least satu kelas
        ]);


        // ❗ Check if exam name already exists
        if (Exam::where('name', $request->name)->exists()) {
            return redirect()->route('admin.exams.read')->with('error', 'Examination with thse name already exists.');
        }

        // ✅ Simpan exam ke table 'exams'
        $exam = Exam::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        // ✅ Attach kelas-kelas yang dipilih (table: classroom_exam)
        $exam->classrooms()->attach($request->classrooms);

        return redirect()->route('admin.exams.read')->with('success', 'Examination added successfully.');
    }

    // Show edit form for a specific exam
    // public function edit($id)
    // {
    //     $exam = Exam::with('classrooms')->findOrFail($id);
    //     return view('admin.exams.edit', compact('exam'));
    // }

    // Update an existing exam
    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // ❗ Check if new name already exists in another exam
        if (Exam::where('name', $request->name)->where('id', '!=', $id)->exists()) {
            return redirect()->back()->with('error', 'Examination with this name already exists.');
        }

        // Fill and check if anything changed
        $exam->fill($request->only('name', 'start_date', 'end_date'));

        if (!$exam->isDirty()) {
            return redirect()->back()->with('error', 'Please update something before submitting.');
        }

        $exam->save();

        return redirect()->route('admin.exams.read')->with('success', 'Examination updated successfully.');
    }



    // Optional: Delete an exam
    public function delete($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();

        return redirect()->route('admin.exams.read')->with('success', 'Examination deleted successfully.');
    }

    public function viewMarks($exam_id, $classroom_id)
    {
        $exam = Exam::findOrFail($exam_id);
        $classroom = Classroom::with('students')->findOrFail($classroom_id);

        // Ambil semua student dan markah mereka untuk exam ni
        $students = $classroom->students;
        $marks = ExamMark::where('exam_id', $exam_id)
            ->where('classroom_id', $classroom_id)
            ->get()
            ->keyBy('student_ic'); // supaya senang akses ikut IC

        return view('admin.exams.viewmarks', compact('exam', 'classroom', 'students', 'marks'));
    }
}
