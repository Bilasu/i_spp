<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuizCategory;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['quiz_categories'] = QuizCategory::get();

        if (Auth::guard('admin')->check()) {
            return view('admin.quizcategory.list', $data);
        } elseif (Auth::guard('teacher')->check()) {
            return view('teacher.quizcategory.list', $data);
        } else {

            if (Auth::guard('student')->check()) {
                $userIc = Auth::guard('student')->user()->ic;

                // Dapatkan quiz categories yang statusnya 'active' sahaja
                $activeCategories = QuizCategory::where('status', 'active')->get();

                foreach ($activeCategories as $category) {
                    $highestMark = QuizResult::where('user_ic', $userIc)
                        ->where('quiz_category_id', $category->id)
                        ->selectRaw('MAX((correct / total) * 100) as max_score')
                        ->value('max_score');

                    $category->highest_mark = $highestMark !== null ? round($highestMark) : null;
                }

                $data['quiz_categories'] = $activeCategories;

                return view('student.quizcategory.list', $data);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        // Semak jika kategori sudah wujud
        // Semak jika kategori sudah wujud
        if (QuizCategory::where('name', $request->name)->exists()) {
            if (Auth::guard('teacher')->check()) {
                return redirect()->route('teacher.quizcategory.read')->with('error', 'Quiz Type with this name already exists.');
            }
            return redirect()->route('admin.quizcategory.read')->with('error', 'Quiz Type with this name already exists.');
        }

        // 1. Buat kategori
        $data = new QuizCategory();
        $data->name = $request->name;
        $data->status = 'active';
        $data->save();

        // Redirect balik ke list kategori (bukan terus ke kuiz)
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.quizcategory.read')
                ->with('success', 'Category Added Successfully. You may now add questions (Kertas 1 / Kertas 2 / Kertas 3).');
        }

        return redirect()->route('admin.quizcategory.read')
            ->with('success', 'Category Added Successfully. You may now add questions (Kertas 1 / Kertas 2 / Kertas 3).');
    }

    /**
     * Display the specified resource.
     */
    public function show(QuizCategory $quizCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuizCategory $quizCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QuizCategory $quizCategory)
    {
        $data = QuizCategory::find($request->id);

        // Check jika user tidak mengubah apa-apa
        if ($request->name === $data->name && $request->status === $data->status) {
            return back()->with('error', 'Please update something before submitting.');
        }

        // Check jika nama yang baru sudah wujud dalam database (exclude current ID)
        $duplicate = QuizCategory::where('name', $request->name)
            ->where('id', '!=', $request->id)
            ->exists();

        if ($duplicate) {
            return back()->with('error', 'Quiz category already exists.');
        }

        // Lulus: Simpan perubahan
        $data->name = $request->name;
        $data->status = $request->status;
        $data->update();

        // Redirect ikut role
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.quizcategory.read')->with('success', 'Quiz Type Updated Successfully');
        }

        return redirect()->route('admin.quizcategory.read')->with('success', 'Quiz Type Updated Successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function delete($id, QuizCategory $quizCategory)
    {
        $data = QuizCategory::find($id);
        $data->delete();

        // Redirect ikut role
        if (Auth::guard('teacher')->check()) {
            return redirect()->route('teacher.quizcategory.read')->with('success', 'Quiz Type Deleted Successfully');
        }

        return redirect()->route('admin.quizcategory.read')->with('success', 'Quiz Type Deleted Successfully');
    }

    public function showByCategory($category_id)
    {
        // Dapatkan kategori berdasarkan ID
        $category = QuizCategory::findOrFail($category_id);

        // Dapatkan semua soalan untuk kategori ini
        $questions = $category->questions;

        // Return view dengan kategori dan soalan-soalan
        return view('questions.by_category', compact('category', 'questions'));
    }
}
