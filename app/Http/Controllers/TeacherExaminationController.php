<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Exam;
use App\Models\Classroom;
use App\Models\ExamMark;
use Carbon\Carbon;

class TeacherExaminationController extends Controller
{
    public function index()
    {
        $teacherIc = Auth::guard('teacher')->user()?->ic;

        // Ambil kelas yang teacher ni ajar (dari classroom_user pivot)
        $classroomIds = DB::table('classroom_user')
            ->where('user_ic', $teacherIc)
            ->where('role', 'teacher')
            ->pluck('classroom_id');

        // Ambil exam yang ada kelas teacher ajar dan paparkan nama kelas
        $exams = Exam::with(['classrooms' => function ($query) use ($classroomIds) {
            $query->whereIn('id', $classroomIds);  // pastikan hanya kelas teacher ajar yang dipaparkan
        }])
            ->get();

        return view('teacher.exams.index', compact('exams'));
    }

    // Form untuk isi markah
    public function fillMarks($examId, $classroomId)
    {
        $teacherIc = Auth::guard('teacher')->user()?->ic;

        // Retrieve classroom and exam models
        $classroom = Classroom::findOrFail($classroomId);
        $exam = Exam::findOrFail($examId);

        // Pastikan kelas ini memang kelas yang teacher ajar
        $isTeacherOfClass = DB::table('classroom_user')
            ->where('user_ic', $teacherIc)
            ->where('classroom_id', $classroom->id)
            ->where('role', 'teacher')
            ->exists();

        if (!$isTeacherOfClass) {
            abort(403, 'Anda tidak dibenarkan akses kelas ini.');
        }

        $today = Carbon::now();
        $canFill = $today->between(Carbon::parse($exam->start_date), Carbon::parse($exam->end_date));

        // Ambil semua pelajar dalam kelas ini
        $students = DB::table('classroom_user')
            ->where('classroom_user.classroom_id', $classroom->id)
            ->where('classroom_user.role', 'student')
            ->join('users', 'users.ic', '=', 'classroom_user.user_ic')
            ->select('users.ic', 'users.name')
            ->get();

        // Ambil markah yang sudah ada
        $marks = ExamMark::where('exam_id', $exam->id)
            ->where('classroom_id', $classroom->id)
            ->get()
            ->keyBy('student_ic');

        // dd([
        //     'hari_ini' => $today,
        //     'start_date' => $exam->start_date,
        //     'end_date' => $exam->end_date,
        //     'boleh_isi' => $canFill
        // ]);
        return view('teacher.exams.fillmarks', compact('exam', 'classroom', 'students', 'marks', 'canFill'));
    }
    // Simpan markah pelajar (update atau insert)
    public function storeMarks(Request $request, Exam $exam, Classroom $classroom)
    {
        $teacherIc = Auth::guard('teacher')->user()?->ic;

        // Pastikan kelas ni memang kelas teacher ajar
        $isTeacherOfClass = DB::table('classroom_user')
            ->where('user_ic', $teacherIc)
            ->where('classroom_id', $classroom->id)
            ->where('role', 'teacher')
            ->exists();

        if (!$isTeacherOfClass) {
            abort(403, 'Anda tidak dibenarkan akses kelas ini.');
        }

        // Semak tarikh valid
        $today = Carbon::now();
        if ($today->lt(Carbon::parse($exam->start_date)) || $today->gt(Carbon::parse($exam->end_date))) {
            return redirect()->route('teacher.exams.index')
                ->with('error', 'Anda tidak dibenarkan isi markah pada waktu ini.');
        }

        // Validate input marks array: expect student_ic => mark
        $data = $request->validate([
            'marks' => 'required|array',
            'marks.*' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($data['marks'] as $student_ic => $mark) {
            ExamMark::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'classroom_id' => $classroom->id,
                    'student_ic' => $student_ic,
                ],
                ['mark' => $mark]
            );
        }

        return redirect()->route('teacher.exams.index', [$exam->id, $classroom->id])
            ->with('success', 'Markah berjaya disimpan.');
    }
}
