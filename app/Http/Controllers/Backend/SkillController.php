<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSkillRequest;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $skills = Skill::all();
        return view('skills.index', compact('skills'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('skills.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'min:3'],
            'image' => ['required', 'image']
        ]);

        if ($request->hasFile('image')) {

            $image = $this->storeImage($request);

            Skill::create([
                'name' => $request->name,
                'image' => $image
            ]);

            return to_route('skills.index')->with('success', 'Skill created.');
        }

        return back();
    }
        

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Skill $skill)
    {
        return view('skills.edit', compact('skill'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Skill $skill)
        {
            $request->validate([
                'name' => ['required', 'min:3'],
                'image' => ['nullable', 'image']
            ]);
    
            $image = $skill->image;
            if ($request->hasFile('image')) {
                Storage::delete($skill->image);
                $image = $this->storeImage($request);
            }
    
            $skill->update([
                'name' => $request->name,
                'image' => $image
            ]);
    
            return to_route('skills.index')->with('success', 'Skill updated.');
        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Skill $skill)
    {
        Storage::delete($skill->image);
        $skill->delete();

        return back()->with('danger', 'Skill deleted.');
    }

    private function storeImage(Request $request) {
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $image = $request->file('image')->storeAs('skills',  Str::lower($request->name) . '.' . $extension);
        return $image;
    }
}
