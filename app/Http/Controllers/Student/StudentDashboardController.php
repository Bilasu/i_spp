<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuizAttempt;
use App\Models\Assignment;
use App\Models\ExamMark;
use App\Models\QuizResult;
use Illuminate\Support\Facades\Auth;

// Add this line if AssignmentSubmission exists in App\Models
use App\Models\Submission;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('student')->user();

        // Total assignments for student's classrooms
        $totalAssignments = Assignment::whereHas('classroom.students', function ($query) use ($user) {
            $query->where('users.ic', $user->ic);
        })->count();

        // Total submissions by this student
        $totalSubmitted = Submission::where('student_ic', $user->ic)->count();

        // Ongoing assignments not past due date
        $ongoingAssignments = Assignment::whereHas('classroom.students', function ($query) use ($user) {
            $query->where('users.ic', $user->ic);
        })
            ->where('due_date', '>=', now())
            ->count();

        // Quizzes attempted by student (check correct column)
        $quizAttempted = QuizResult::where('user_ic', $user->ic)->count();

        // Dapatkan markah peperiksaan beserta nama exam
        $examMarks = ExamMark::with('exam')
            ->where('student_ic', $user->ic)
            ->get()
            ->map(function ($mark) {
                return [
                    'exam' => $mark->exam->name ?? 'Tanpa Nama',
                    'mark' => $mark->mark,
                ];
            });
        //dd($examMarks);
        return view('student.dashboard', compact(
            'totalAssignments',
            'totalSubmitted',
            'ongoingAssignments',
            'quizAttempted',
            'examMarks',
            'user', // Add user to the view data
        ));
    }
}
