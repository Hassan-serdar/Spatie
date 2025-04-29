<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;



class ArticleController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:View Articles',only:['index']),
            new Middleware('permission:Edit Articles',only:['edit']),
            new Middleware('permission:Create Articles',only:['create']),
            new Middleware('permission:Delete Articles',only:['destroy']),
        ];
    }

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
        try {
            $validated = request()->validate([
                'title' => 'required|min:3|max:60',
                'text' => 'required',
                'author' => 'required|string|min:3|max:30'
            ]);
    
            $userId = $request->user()->id;
            Article::create([
                'title' => $validated['title'],
                'text' => $validated['text'],
                'author' => $validated['author'],
                'user_id' => $userId,
            ]);
    
            return redirect()->route('articles.index')->with('success','Articles added successfully');
        } catch (ValidationException $e) {
            Log::error('Validation failed while creating article: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create article: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while creating the article.')->withInput();
        }
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

        $this->authorize('update', $article);
        return view('articles.edit',[
            'article'=>$article,
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $article = Article::findOrFail($id);
    
            $validated = request()->validate([
                'title' => 'required|regex:/[a-zA-Z\s]+/|min:3|max:60',
                'text' => 'required',
                'author' => 'required|string|min:3|max:30',
                Rule::unique('articles')->ignore($id),
            ]);
    
            $article->update($validated);
    
            return redirect()->route('articles.index')->with('success', 'Article Updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed while updating article (ID: ' . $id . '): ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update article (ID: ' . $id . '): ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while updating the article.')->withInput();
        }
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $article = Article::findOrFail($id);
            $article->delete();
            return redirect()->route('articles.index')->with('success','Article Deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete article (ID: ' . $id . '): ' . $e->getMessage());
            return redirect()->route('articles.index')->with('error', 'Failed to delete article.');
        }
    }
    }
