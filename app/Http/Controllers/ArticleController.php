<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles=Article::latest()->paginate(25);
        return view('articles.list',[
            'articles'=>$articles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage. 
     */
    public function store(Request $request)
    {
        $validated=request()->validate([
            'title'=>'required|min:3|max:60',
            'text'=>'required',
            'author'=>'required|string|min:3|max:30'
        ]);

        Article::create([
            'title'=>$validated['title'],
            'text'=>$validated['text'],
            'author'=>$validated['author'],
        ]);
        return redirect()->route('articles.index')->with('success','Articles added successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $article=Article::findOrFail($id);
        return view('articles.edit',[
            'article'=>$article,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $article=Article::findOrFail($id);
        $validated=request()->validate([
            'title'=>'required|regex:/[a-zA-Z\s]+/|min:3|max:60',
            'text'=>'required',
            'author'=>'required|string|min:3|max:30',
            Rule::unique('articles')->ignore($id),
        ]);
        $article->update($validated);
        return redirect()->route('articles.index')->with('success', 'Article Updated successfully.');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article=Article::findOrFail($id);
        $article->delete();
        return redirect()->route('articles.index')->with('success','Article Deleted successfully');

    }
}
