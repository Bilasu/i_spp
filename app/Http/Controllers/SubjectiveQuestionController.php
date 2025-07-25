<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\SubjectiveQuestion;
use App\Models\QuizCategory;

class SubjectiveQuestionController extends Controller
{
    public function index($quiz_category_id)
    {

        // Tentukan user dari guard yang sedang aktif
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
        } elseif (Auth::guard('teacher')->check()) {
            $user = Auth::guard('teacher')->user();
        } elseif (Auth::guard('student')->check()) {
            $user = Auth::guard('student')->user();
        } else {
            abort(403, 'Unauthorized'); // Tiada sesi login aktif
        }

        // Ambil semua kategori untuk dropdown modal
        $categories = QuizCategory::all();

        // Check role dan ambil data ikut role
        if ($user->role === 'admin') {
            $questions = SubjectiveQuestion::with('category')
                ->where('quiz_category_id', $quiz_category_id)
                ->get();

            return view('admin.quiz.subjective', compact('questions', 'categories', 'quiz_category_id'));
        } elseif ($user->role === 'teacher') {
            $questions = SubjectiveQuestion::with('category')
                // ->where('created_by', $user->ic)
                ->where('quiz_category_id', $quiz_category_id)
                ->get();

            return view('teacher.quiz.subjective', compact('questions', 'categories', 'quiz_category_id'));
        } else {
            abort(403, 'Access denied.');
        }
    }


    public function store(Request $request)
    {
        if ($request->isMethod('get')) {
            $categories = QuizCategory::all();
            return view('subjective_questions.create', compact('categories'));
        }

        // Validate input
        $request->validate([
            'question' => 'required|string',
            'mark_total' => 'required|numeric|min:5, max:100',
            'quiz_category_id' => 'required|exists:quiz_categories,id',
        ]);

        // Dapatkan user dari guard yang aktif
        $user = Auth::guard('admin')->user()
            ?? Auth::guard('teacher')->user()
            ?? Auth::guard('student')->user();

        if (!$user) {
            abort(403, 'Unauthorized access.');
        }

        // Simpan soalan subjektif
        SubjectiveQuestion::create([
            'question' => $request->question,
            'mark_total' => $request->mark_total,
            'quiz_category_id' => $request->quiz_category_id,
            'created_by' => $user->ic,
        ]);
        // Redirect berdasarkan role pengguna
        if ($user->role === 'admin') {
            return redirect()->route('admin.subjective.read', ['quiz_category_id' => $request->quiz_category_id])->with('success', 'Soalan subjektif berjaya ditambah');
        } elseif ($user->role === 'teacher') {
            return redirect()->route('teacher.subjective.read', ['quiz_category_id' => $request->quiz_category_id])->with('success', 'Soalan subjektif berjaya ditambah');
        } else {
            abort(403, 'Unauthorized access.');
        }
    }



    public function update(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'question' => 'required|string',
            'mark_total' => 'required|numeric|min:5, max:100',
            'quiz_category_id' => 'required|exists:quiz_categories,id',
        ]);

        // Find the question to update
        $question = SubjectiveQuestion::findOrFail($id);

        // Check if there's no change in the question
        if (
            $question->question === $request->question &&
            $question->mark_total === $request->mark_total &&
            $question->quiz_category_id == $request->quiz_category_id
        ) {
            return back()->with(['error' => 'Please make some changes before submitting.']);
        }

        // Update the question if there are changes
        $question->update([
            'question' => $request->question,
            'mark_total' => $request->mark_total,
            'quiz_category_id' => $request->quiz_category_id,
        ]);

        // Get the authenticated user using the correct guard
        $user = Auth::guard('admin')->user()
            ?? Auth::guard('teacher')->user()
            ?? Auth::guard('student')->user();

        // If no user is authenticated, abort with Unauthorized access
        if (!$user) {
            abort(403, 'Unauthorized access.');
        }

        // Redirect based on role of the authenticated user
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.subjective.read', ['quiz_category_id' => $request->quiz_category_id])
                    ->with('success', 'Question updated successfully.');
            case 'teacher':
                return redirect()->route('teacher.subjective.read', ['quiz_category_id' => $request->quiz_category_id])
                    ->with('success', 'Question updated successfully.');
            default:
                abort(403, 'Unauthorized access.');
        }
    }



    public function delete($id)
    {
        $question = SubjectiveQuestion::findOrFail($id);
        $categoryId = $question->quiz_category_id;
        $question->delete();

        // Redirect ikut guard / role
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.subjective.read', ['quiz_category_id' => $categoryId])
                ->with('success', 'Question deleted successfully.');
        }

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.subjective.read', ['quiz_category_id' => $categoryId])
                ->with('success', 'Question deleted successfully.');
        }

        abort(403, 'Unauthorized access.');
    }

    // public function show($id)
    // {
    //     // Cari soalan berdasarkan ID
    //     $question = SubjectiveQuestion::with('answers.student')->findOrFail($id);

    //     // Semak jika pengguna yang log masuk adalah guru yang mencipta soalan
    //     if (Auth::user()->id !== $question->created_by && Auth::user()->role !== 'admin') {
    //         // Jika tidak, hanya benarkan akses untuk melihat sahaja, tidak boleh beri markah/komentar
    //         $canGiveFeedback = false;
    //     } else {
    //         // Jika ya, boleh beri markah dan komen
    //         $canGiveFeedback = true;
    //     }

    //     // Tentukan view berdasarkan peranan pengguna
    //     if (Auth::user()->role === 'teacher') {
    //         return view('teacher.quiz.subshow', compact('question', 'canGiveFeedback'));
    //     } elseif (Auth::user()->role === 'admin') {
    //         return view('admin.quiz.subshow', compact('question', 'canGiveFeedback'));
    //     } else {
    //         abort(403, 'Unauthorized access.');
    //     }
    // }

    public function subshow($categoryId, $questionId)
    {

        $teacherIc = Auth::guard('teacher')->user()->ic;

        // Soalan yang diminta
        $question = SubjectiveQuestion::with('answers.student')->findOrFail($questionId);

        // Pastikan soalan tersebut betul-betul milik cikgu
        if ($question->created_by === $teacherIc) {
            // Soalan milik cikgu, terus return view dengan markah dan komen
            $category = QuizCategory::findOrFail($categoryId);
            $isOwner = true;
            return view('teacher.quiz.subshow', compact('category', 'question', 'isOwner'));
        } else {
            // Soalan bukan milik cikgu, hanya paparkan markah, komen, nama pelajar, dan jawapan pelajar
            $isOwner = false;
            return view('teacher.quiz.subshow', compact('question', 'isOwner'));
        }
    }

    public function review($categoryId, $questionId)
    {
        // Ambil soalan berdasarkan ID sahaja
        $question = SubjectiveQuestion::with(['answers.student', 'category'])->findOrFail($questionId);

        // Paparkan view dengan hanya soalan yang dipilih
        return view('teacher.answer.review', compact('question'));
    }


    public function adminSubshow($categoryId, $questionId)
    {

        $adminIc = Auth::guard('admin')->user()->ic;
        // Soalan yang diminta
        $question = SubjectiveQuestion::with('answers.student')->findOrFail($questionId);


        // Pastikan soalan tersebut betul-betul milik cikgu
        if ($question->created_by === $adminIc) {
            // Soalan milik cikgu, terus return view dengan markah dan komen
            $category = QuizCategory::findOrFail($categoryId);
            $isOwner = true;
            return view('admin.quiz.subshow', compact('category', 'question', 'isOwner'));
        } else {
            // Soalan bukan milik cikgu, hanya paparkan markah, komen, nama pelajar, dan jawapan pelajar
            $isOwner = false;
            return view('admin.quiz.subshow', compact('question', 'isOwner'));
        }
    }

    // public function adminReview($categoryId)
    // {
    //     $category = QuizCategory::with('questions.answers.user')->findOrFail($categoryId);

    //     // Redirect kalau admin ialah pencipta
    //     if ($category->user_ic !== Auth::guard('admin')->user()->ic) {
    //         return redirect()->route('admin.quiz.subshow', $categoryId);
    //     }

    //     return view('admin.answer.review', compact('category'));
    // }
}
