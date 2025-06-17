<?php

namespace App\Http\Controllers;

use App\Models\EssayQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\QuizCategory;

class EssayQuestionController extends Controller
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
            $questions = EssayQuestion::with('category')
                ->where('quiz_category_id', $quiz_category_id)
                ->get();

            return view('admin.quiz.essay', compact('questions', 'categories', 'quiz_category_id'));
        } elseif ($user->role === 'teacher') {
            $questions = EssayQuestion::with('category')
                // ->where('created_by', $user->ic)
                ->where('quiz_category_id', $quiz_category_id)
                ->get();

            return view('teacher.quiz.essay', compact('questions', 'categories', 'quiz_category_id'));
        } else {
            abort(403, 'Access denied.');
        }
    }


    public function store(Request $request)
    {
        if ($request->isMethod('get')) {
            $categories = QuizCategory::all();
            return view('essay_questions.create', compact('categories'));
        }

        // Validate input
        $request->validate([
            'question' => 'required|string',
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
        EssayQuestion::create([
            'question' => $request->question,
            'quiz_category_id' => $request->quiz_category_id,
            'created_by' => $user->ic,
        ]);
        // Redirect berdasarkan role pengguna
        if ($user->role === 'admin') {
            return redirect()->route('admin.essay.read', ['quiz_category_id' => $request->quiz_category_id])->with('success', 'Soalan subjektif berjaya ditambah');
        } elseif ($user->role === 'teacher') {
            return redirect()->route('teacher.essay.read', ['quiz_category_id' => $request->quiz_category_id])->with('success', 'Soalan subjektif berjaya ditambah');
        } else {
            abort(403, 'Unauthorized access.');
        }
    }



    public function update(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'question' => 'required|string',
            'quiz_category_id' => 'required|exists:quiz_categories,id',
        ]);

        // Find the question to update
        $question = EssayQuestion::findOrFail($id);

        // Check if there's no change in the question
        if (
            $question->question === $request->question &&
            $question->quiz_category_id == $request->quiz_category_id
        ) {
            return back()->with(['error' => 'Please make some changes before submitting.']);
        }

        // Update the question if there are changes
        $question->update([
            'question' => $request->question,
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
                return redirect()->route('admin.essay.read', ['quiz_category_id' => $request->quiz_category_id])
                    ->with('success', 'Question updated successfully.');
            case 'teacher':
                return redirect()->route('teacher.essay.read', ['quiz_category_id' => $request->quiz_category_id])
                    ->with('success', 'Question updated successfully.');
            default:
                abort(403, 'Unauthorized access.');
        }
    }



    public function delete($id)
    {
        $question = EssayQuestion::findOrFail($id);
        $categoryId = $question->quiz_category_id;
        $question->delete();

        // Redirect ikut guard / role
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.essay.read', ['quiz_category_id' => $categoryId])
                ->with('success', 'Soalan berjaya dipadam.');
        }

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.essay.read', ['quiz_category_id' => $categoryId])
                ->with('success', 'Soalan berjaya dipadam.');
        }

        abort(403, 'Unauthorized access.');
    }

    // public function show($id)
    // {
    //     // Cari soalan berdasarkan ID
    //     $question = EssayQuestion::with('answers.student')->findOrFail($id);

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
        $question = EssayQuestion::with('answers.student')->findOrFail($questionId);

        // Pastikan soalan tersebut betul-betul milik cikgu
        if ($question->created_by === $teacherIc) {
            // Soalan milik cikgu, terus return view dengan markah dan komen
            $category = QuizCategory::findOrFail($categoryId);
            $isOwner = true;
            return view('teacher.quiz.essayshow', compact('category', 'question', 'isOwner'));
        } else {
            // Soalan bukan milik cikgu, hanya paparkan markah, komen, nama pelajar, dan jawapan pelajar
            $isOwner = false;
            return view('teacher.quiz.essayshow', compact('question', 'isOwner'));
        }
    }

    public function review($categoryId, $questionId)
    {
        // Ambil soalan berdasarkan ID sahaja
        $question = EssayQuestion::with(['answers.student', 'category'])->findOrFail($questionId);

        // Paparkan view dengan hanya soalan yang dipilih
        return view('teacher.answer.review', compact('question'));
    }


    public function adminSubshow($categoryId, $questionId)
    {

        $adminIc = Auth::guard('admin')->user()->ic;
        // Soalan yang diminta
        $question = EssayQuestion::with('answers.student')->findOrFail($questionId);

        // Pastikan soalan tersebut betul-betul milik cikgu
        if ($question->created_by === $adminIc) {
            // Soalan milik cikgu, terus return view dengan markah dan komen
            $category = QuizCategory::findOrFail($categoryId);
            $isOwner = true;
            return view('admin.quiz.essayshow', compact('category', 'question', 'isOwner'));
        } else {
            // Soalan bukan milik cikgu, hanya paparkan markah, komen, nama pelajar, dan jawapan pelajar
            $isOwner = false;
            return view('admin.quiz.essayshow', compact('question', 'isOwner'));
        }
    }

    public function adminReview($categoryId)
    {
        $category = QuizCategory::with('questions.answers.user')->findOrFail($categoryId);

        // Redirect kalau admin ialah pencipta
        if ($category->user_ic !== Auth::guard('admin')->user()->ic) {
            return redirect()->route('admin.quiz.essayshow', $categoryId);
        }

        return view('admin.answer.review', compact('category'));
    }
}
