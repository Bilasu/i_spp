<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Notes;
use App\Models\QuizCategory;

class TeacherDashboardController extends Controller
{

    public function index()
    {
        $teacher = Auth::guard('teacher')->user();
        $classes = $teacher->classrooms;  // Get the classes assigned to the teacher
        // 1. Summary Cards
        $classes = $teacher->classrooms()->wherePivot('role', 'teacher')->get();
        $classIds = $classes->pluck('id'); // Ini ok

        // Students under teacher’s classes
        $totalStudents = User::whereHas('classrooms', function ($q) use ($classIds) {
            $q->whereIn('classroom_id', $classIds)
                ->where('classroom_user.role', 'student');
        })->distinct()->count();


        $totalClasses = $classes->count();

        $ongoingExams = Exam::whereHas('classrooms', function ($q) use ($classIds) {
            $q->whereIn('classroom_id', $classIds);
        })->where('end_date', '>=', now())->count();

        $totalMarksSubmitted = ExamMark::whereHas('classroom.teachers', function ($q) use ($teacher) {
            $q->where('user_ic', $teacher->ic);
        })->count();

        // $totalNotes = Notes::where('uploaded_by', $teacher->ic)->count();

        // $totalQuizCategories = QuizCategory::where('created_by', $teacher->ic)->count();

        $gradeChartData = [];
        $averageScores = [];

        foreach ($classes as $class) {
            $students = $class->students;
            $grades = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'F' => 0];
            $totalScore = 0;
            $count = 0;

            // Get all the marks for this class from the exam_marks table
            $examMarks = \App\Models\ExamMark::where('classroom_id', $class->id)->get();

            foreach ($examMarks as $mark) {
                $score = $mark->mark; // Use the 'mark' column from the exam_marks table
                $grade = $this->getGradeFromScore($score);
                $grades[$grade]++;
                $totalScore += $score;
                $count++;
            }


            $gradeChartData[] = [
                'class' => $class->class_name,
                'grades' => $grades,  // Assign grade counts per class
            ];
            //dd($gradeChartData);
            $averageScores[] = [
                'class' => $class->class_name,
                'average' => $count > 0 ? round($totalScore / $count, 2) : 0
            ];
        }

        return view('teacher.dashboard', compact(
            'totalStudents',
            'totalClasses',
            'ongoingExams',
            'totalMarksSubmitted',
            // 'totalNotes',
            // 'totalQuizCategories',
            'gradeChartData',
            'averageScores'
        ));
    }

    private function getGradeFromScore($score)
    {
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C';
        if ($score >= 50) return 'D';
        if ($score >= 40) return 'E';
        return 'F';
    }
}
