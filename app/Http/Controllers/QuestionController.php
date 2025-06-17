<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\QuizCategory;
use App\Models\QuizResult; // Import the QuizResult model
use Symfony\Component\Console\Question\Question as QuestionQuestion;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{

    public function index(Request $request, $id)
    {
        $category = QuizCategory::findOrFail($id);
        $questions = $category->questions; // make sure method name in model is "questions"

        if (Auth::guard('teacher')->check()) {
            return view('teacher.quiz.list', compact('category', 'questions'));
        }

        return view('admin.quiz.list', compact('category', 'questions'));
        // Merge $data and compacted $notetypes
        // $data = array_merge($data, compact('notetypes'));

        // dd($data); // Dumping the merged data to see if it's passed correctly

        // if (Auth::guard('admin')->check()) {
        // return view('admin.quiz.list', $data);
        // } else {
        //     return view('teacher.quiz.list', $data);
        // }
    }
    // //
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'opa' => 'required',
            'opb' => 'required',
            'opc' => 'required',
            'opd' => 'required',
            'ans' => 'required',
            'quiz_category_id' => 'required|exists:quiz_categories,id',
        ]);

        $q = new Question();
        $q->question = $request->question;
        $q->a = $request->opa;
        $q->b = $request->opb;
        $q->c = $request->opc;
        $q->d = $request->opd;
        $q->ans = $request->ans;
        $q->quiz_category_id = $request->quiz_category_id;
        $q->save();

        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.quiz.read', $request->quiz_category_id)
                ->with('success', 'Soalan berjaya ditambah.');
        }

        return redirect()->route('admin.quiz.read', $request->quiz_category_id)
            ->with('success', 'Soalan berjaya ditambah.');
    }

    // public function showByCategory($id)
    // {
    //     $category = QuizCategory::findOrFail($id);
    //     $questions = $category->questions; // ambil semua soalan kategori ini

    //     return view('admin.quiz.create', compact('category', 'questions'));
    // }

    // public function show()
    // {
    //     // Dapatkan ID soalan seterusnya dari session, mula dengan 1 kalau tiada
    //     $nextQ = Session::get('nextq', 1);

    //     // Cari satu soalan berdasarkan ID
    //     $question = Question::find($nextQ);

    //     // Jika soalan tiada (mungkin ID lebih dari jumlah soalan), boleh redirect atau tamat kuiz
    //     if (!$question) {
    //         return redirect('student.quiz.end'); // Halaman tamat kuiz
    //     }

    //     return view('admin.quiz.question')->with(['question' => $question]);
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required',
            'opa' => 'required',
            'opb' => 'required',
            'opc' => 'required',
            'opd' => 'required',
            'ans' => 'required|in:a,b,c,d',
        ]);

        $question = Question::findOrFail($id);

        // Check kalau user tak ubah apa-apa
        if (
            $request->question === $question->question &&
            $request->opa === $question->a &&
            $request->opb === $question->b &&
            $request->opc === $question->c &&
            $request->opd === $question->d &&
            $request->ans === $question->ans
        ) {
            return back()->with('error', 'Please update something before submitting.');
        }

        // Update fields
        $question->question = $request->question;
        $question->a = $request->opa;
        $question->b = $request->opb;
        $question->c = $request->opc;
        $question->d = $request->opd;
        $question->ans = $request->ans;

        $question->save();

        // Redirect ikut role
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.quiz.read', $question->quiz_category_id)
                ->with('success', 'Soalan berjaya dikemaskini.');
        }

        return redirect()->route('admin.quiz.read', $question->quiz_category_id)
            ->with('success', 'Question updated successfully.');
    }

    public function delete($id)
    {
        $data = Question::find($id);
        $categoryId = $data->quiz_category_id;
        $data->delete();

        // Redirect ikut role
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.quiz.read', $categoryId)
                ->with('success', 'Soalan berjaya dipadam.');
        }

        return redirect()->route('admin.quiz.read', $categoryId)
            ->with('success', 'Question deleted successfully.');
    }



    public function startquiz($quiz_category_id)
    {
        // Store category ID in session
        Session::put('quiz_category_id', $quiz_category_id);
        Session::put('nextq', 0); // Initialize to the first question
        Session::put('correctans', 0); // Initialize correct answer count
        Session::put('wrongans', 0); // Initialize wrong answer count

        // Retrieve the category and questions
        $category = QuizCategory::findOrFail($quiz_category_id);
        $questions = $category->questions->values();

        if ($questions->isEmpty()) {
            // No questions found, display appropriate message
            return view('student.quiz.list', ['no_questions' => true]);
        }

        // Get the current question index from session
        $currentIndex = Session::get('nextq', 0); // Retrieve next question index from session
        $question = $questions[$currentIndex]; // Get the question based on current index

        return view('student.quiz.list', [
            'quiz_category' => $category,
            'question' => $question,
            'currentIndex' => $currentIndex,
            'totalQuestions' => count($questions),
        ]);
    }


    public function submitans(Request $request)
    {
        $nextq = Session::get('nextq', 0);
        $quiz_category_id = Session::get('quiz_category_id');
        $questions = QuizCategory::findOrFail($quiz_category_id)->questions->values();

        $request->validate([
            'ans' => 'required',
            'dbnans' => 'required',
            'question_id' => 'required|integer',
        ]);

        // Simpan jawapan pelajar dalam session
        $answers = Session::get('answers', []);
        $answers[$request->question_id] = $request->ans;
        Session::put('answers', $answers);

        // Cek betul/salah
        if ($request->ans === $request->dbnans) {
            Session::put('correctans', Session::get('correctans', 0) + 1);
        } else {
            Session::put('wrongans', Session::get('wrongans', 0) + 1);
        }

        // Soalan seterusnya
        $nextq++;
        Session::put('nextq', $nextq);

        if ($nextq >= $questions->count()) {
            // Tamat kuiz
            $correct = Session::get('correctans');
            $wrong = Session::get('wrongans');
            $latestResult = QuizResult::create([
                'user_ic' => Auth::guard('student')->user()->ic,
                'quiz_category_id' => $quiz_category_id,
                'correct' => $correct,
                'wrong' => $wrong,
                'total' => $questions->count(),
                'taken_at' => now(),
            ]);
            $previousResults = QuizResult::where('user_ic', Auth::guard('student')->user()->ic)
                ->where('quiz_category_id', $quiz_category_id)
                ->where('id', '!=', $latestResult->id)
                ->orderByDesc('taken_at')
                ->get();

            Session::forget(['nextq', 'correctans', 'wrongans', 'quiz_category_id', 'answers']);
            return view('student.quiz.end', compact('latestResult', 'previousResults'));
        }

        $question = $questions[$nextq];
        return view('student.quiz.list', [
            'question' => $question,
            'quiz_category' => QuizCategory::findOrFail($quiz_category_id),
            'currentIndex' => $nextq,
            'totalQuestions' => $questions->count(),
            'selectedAnswer' => Session::get('answers')[$question->id] ?? null,
        ]);
    }



    public function showResults($quiz_id)
    {
        $quizResults = QuizResult::with(['user.classrooms'])
            ->where('quiz_category_id', $quiz_id)
            ->get();

        // Check role via guard (assuming separate guards for admin & teacher)
        if (Auth::guard('admin')->check()) {
            return view('admin.quiz.quizresults', compact('quizResults'));
        } elseif (Auth::guard('teacher')->check()) {
            return view('teacher.quiz.quizresults', compact('quizResults'));
        } else {
            abort(403, 'Unauthorized');
        }
    }

    public function allResults()
    {
        $userIc = Auth::guard('student')->user()->ic;

        // Ambil semua keputusan pelajar dan group by kategori
        $resultsByCategory = DB::table('quiz_results')
            ->join('quiz_categories', 'quiz_results.quiz_category_id', '=', 'quiz_categories.id') // betulkan sini
            ->select(
                'quiz_categories.id as category_id',
                'quiz_categories.name as category_name',
                'quiz_results.correct',
                'quiz_results.wrong',
                'quiz_results.total',
                'quiz_results.taken_at'
            )
            ->where('quiz_results.user_ic', $userIc)
            ->orderBy('quiz_results.taken_at', 'desc')
            ->get()
            ->groupBy('category_name');

        return view('student.quiz.all_results', compact('resultsByCategory'));
    }



    public function back($index)
    {
        $quiz_category_id = Session::get('quiz_category_id');
        $questions = QuizCategory::findOrFail($quiz_category_id)->questions->values();

        $index = max(0, $index);
        $question = $questions[$index];

        Session::put('nextq', $index);

        // Ambil semula jawapan jika ada
        $answers = Session::get('answers', []);
        $selectedAnswer = $answers[$question->id] ?? null;

        return view('student.quiz.list', [
            'quiz_category' => QuizCategory::findOrFail($quiz_category_id),
            'question' => $question,
            'currentIndex' => $index,
            'totalQuestions' => $questions->count(),
            'selectedAnswer' => $selectedAnswer,
        ]);
    }
}
