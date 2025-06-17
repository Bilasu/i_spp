<?php

namespace App\Http\Controllers;

use App\Models\Notetypes;
use Illuminate\Http\Request;

class NotetypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['notetypes'] = Notetypes::get();
        return view('admin.notetypes.list', $data);
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
        if (Notetypes::where('name', $request->name)->exists()) {
            return redirect()->route('notetypes.read')->with('error', 'Note Type with this name already exists.');
        }


        $data = new Notetypes();
        $data->name = $request->name;
        $data->save();
        return redirect()->route('notetypes.read')->with('success', 'Note Type Added Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notetypes $notetypes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notetypes $notetypes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notetypes $notetypes)
    {
        $data = Notetypes::find($request->id);
        // Check if the name already exists in the database
        $existingNotetype = Notetypes::where('name', $request->name)->first();

        if ($existingNotetype && $existingNotetype->id !== $data->id) {
            return back()->with('error', 'This note type already exists.');
        }
        // Check jika user tidak mengubah apa-apa
        if ($request->name === $data->name) {
            return back()->with('error', 'Please update something before submitting.');
        }
        // Check jika nama yang baru sudah ada dalam database
        $data->name = $request->name;
        $data->update();
        return redirect()->route('notetypes.read')->with('success', 'Note Type Updated Successfully');
        // dd($request->name);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $data = Notetypes::find($id);
        $data->delete();
        return redirect()->route('notetypes.read')->with('success', 'Note Type Deleted Successfully');
    }
}
