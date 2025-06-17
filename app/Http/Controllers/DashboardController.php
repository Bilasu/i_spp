<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Exam;
use App\Models\ExamMark;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalClasses = Classroom::count();

        $ongoingPengisian = Exam::whereHas('examMarks', function ($q) {
            $q->whereNull('mark');
        })->count();

        $totalMarkahDiisi = ExamMark::whereNotNull('mark')->count();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'totalClasses',
            'ongoingPengisian',
            'totalMarkahDiisi'
        ));
    }

    // Endpoint API untuk data chart gred
    public function graphBarGred()
    {
        $exams = Exam::with('examMarks')->get();
        $data = [];

        foreach ($exams as $exam) {
            $grades = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];

            foreach ($exam->examMarks as $mark) {
                $score = $mark->mark;
                if ($score === null) continue;

                if ($score >= 80) $grades['A']++;
                elseif ($score >= 65) $grades['B']++;
                elseif ($score >= 50) $grades['C']++;
                elseif ($score >= 40) $grades['D']++;
                else $grades['E']++;
            }

            $data[] = [
                'exam' => $exam->name,
                'grades' => array_values($grades)
            ];
        }

        // Cek jika semua grades adalah 0
        $isEmpty = collect($data)->every(function ($item) {
            return collect($item['grades'])->every(fn($g) => $g === 0);
        });

        if ($isEmpty) {
            return response()->json([]);
        }

        return response()->json($data);
    }



    public function graphPurataKelas()
    {
        $exams = Exam::all();
        $classes = Classroom::with('students')->get();
        $result = [];

        foreach ($classes as $class) {
            $avg = [];

            foreach ($exams as $exam) {
                $total = 0;
                $count = 0;

                foreach ($class->students as $student) {
                    $mark = ExamMark::where('exam_id', $exam->id)
                        ->where('student_ic', $student->ic)
                        ->first();

                    if ($mark && $mark->mark !== null) {
                        $total += $mark->mark;
                        $count++;
                    }
                }

                $avg[] = $count ? round($total / $count, 2) : 0;
            }

            $result[] = [
                'label' => $class->class_name,
                'data' => $avg,
                'fill' => false,
                'borderColor' => '#' . substr(md5($class->class_name), 0, 6),
                'tension' => 0.1
            ];
        }

        // Jika semua purata adalah 0, return empty array
        $isEmpty = collect($result)->every(function ($item) {
            return collect($item['data'])->every(fn($d) => $d === 0);
        });

        if ($isEmpty) {
            return response()->json([
                'labels' => [],
                'datasets' => []
            ]);
        }

        return response()->json([
            'labels' => $exams->pluck('name'),
            'datasets' => $result
        ]);
    }
}
