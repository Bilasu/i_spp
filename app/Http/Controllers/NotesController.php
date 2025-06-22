<?php

namespace App\Http\Controllers;

use App\Models\Notes;
use App\Models\Notetypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;

class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notetypes = Notetypes::all();
        $data['notes'] = Notes::get();

        // Merge $data and compacted $notetypes
        $data = array_merge($data, compact('notetypes'));

        // dd($data); // Dumping the merged data to see if it's passed correctly

        if (Auth::guard('admin')->check()) {
            return view('admin.notes.list', $data);
        } else {
            return view('teacher.notes.list', $data);
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
            'desc' => 'required',
            'file' => 'required|mimes:pdf,xls,xlsx,ppt,pptx|max:1048576',
            'notetypes_id' => 'required',
        ]);

        $data = new Notes();
        $data->desc = $request->desc;
        $data->notetypes_id = $request->notetypes_id;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();

            // Buat folder jika belum ada
            if (!Storage::disk('public')->exists('uploads')) {
                Storage::disk('public')->makeDirectory('uploads');
            }

            // Bersihkan nama fail (buang space, tanda pelik, dan buat lowercase)
            $cleanFilename = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            // Simpan fail
            Storage::disk('public')->putFileAs('uploads', $file, $cleanFilename);

            // Simpan nama fail dalam database
            $data->file = $cleanFilename;
        }
        // $path = Storage::disk('public')->path('uploads/' . $originalFilename);
        // dd($path, file_exists($path));
        $data->save();

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.notes.read')->with('success', 'New Note Added Successfully');
        } else {
            return redirect()->route('teacher.notes.read')->with('success', 'New Note Added Successfully');
        }
    }




    /**
     * Display the specified resource.
     */
    public function download($file)
    {

        return response()->download(public_path('uploads/' . $file));
    }

    // public function view($file)
    // {
    //     $path = public_path('uploads/' . $file);

    //     if (file_exists($path)) {
    //         $extension = pathinfo($file, PATHINFO_EXTENSION);

    //         if (in_array($extension, ['xls', 'xlsx', 'ppt', 'pptx'])) {
    //             $url = asset('uploads/' . $file);
    //             return redirect('https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode($url));
    //         }

    //         return response()->file($path);
    //     } else {
    //         abort(404, 'File not found');
    //     }
    // }






    public function update(Request $request, $id)
    {
        $request->validate([
            // 'name' => 'required',
            'desc' => 'required',
            'notetypes_id' => 'required',
            'file' => 'nullable|mimes:pdf,xls,xlsx,ppt,pptx|max:1048576',
        ]);

        $notes = Notes::findOrFail($id);

        // Check kalau user tak ubah apa-apa
        if (
            // $request->name === $notes->name &&
            $request->desc === $notes->desc &&
            $request->notetypes_id == $notes->notetypes_id &&
            !$request->hasFile('file')
        ) {
            return back()->with('error', 'Please update something before submitting.');
        }

        // $notes->name = $request->name;
        $notes->desc = $request->desc;
        $notes->notetypes_id = $request->notetypes_id;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();

            // Buat folder jika belum ada
            if (!Storage::disk('public')->exists('uploads')) {
                Storage::disk('public')->makeDirectory('uploads');
            }

            // Bersihkan nama fail (buang space, tanda pelik, dan buat lowercase)
            $cleanFilename = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            // Simpan fail
            Storage::disk('public')->putFileAs('uploads', $file, $cleanFilename);

            // Simpan nama fail dalam database
            $notes->file = $cleanFilename;
        }
        $path = Storage::disk('public')->path('uploads/' . $originalFilename);
        dd($path, file_exists($path));

        $notes->save();

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.notes.read')->with('success', 'Note Updated Successfully');
        } else {
            return redirect()->route('teacher.notes.read')->with('success', 'Note Updated Successfully');
        }
    }


    public function delete($id)
    {
        $data = Notes::find($id);
        $data->delete();
        return redirect()->back()->with('success', 'Note deleted Successfully');
    }
}
