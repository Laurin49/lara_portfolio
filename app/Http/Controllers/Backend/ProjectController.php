<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::all();
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $skills = Skill::all();
        return view('projects.create', compact('skills'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'min:3'],
            'project_url' => ['nullable', 'url'],
            'skill_id' => ['required'],
            'image' => ['required', 'image']
        ]);

        if ($request->hasFile('image')) {

            $image = $this->storeImage($request);

            Project::create([
                'skill_id' => $request->skill_id,
                'name' => $request->name,
                'project_url' => $request->project_url,
                'image' => $image
            ]);

            return to_route('projects.index')->with('success', 'Project created.');
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
    public function edit(Project $project)
    {
        $skills = Skill::all();
        return view('projects.edit', compact('project', 'skills'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => ['required', 'min:3'],
            'project_url' => ['nullable', 'url'],
            'skill_id' => ['required'],
            'image' => ['nullable', 'image']
        ]);

        $image = $project->image;

        if ($request->hasFile('image')) {
            Storage::delete($project->image);

            $image = $this->storeImage($request);
        }

        $project->update([
            'skill_id' => $request->skill_id,
            'name' => $request->name,
            'project_url' => $request->project_url,
            'image' => $image
        ]);

        return to_route('projects.index')->with('success', 'Project updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        Storage::delete($project->image);
        $project->delete();

        return back()->with('danger', 'Project deleted.');
    }

    private function storeImage(Request $request) {
        $file = $request->file('image');
        $file_name = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tmp_name = str_replace(' ', '_', Str::lower($request->name));
        $image = $request->file('image')->storeAs('projects',  $tmp_name . '.' . $extension);
        return $image;
    }
}
