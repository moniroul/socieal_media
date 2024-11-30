<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

    public function index()
    {

        return Post::with(['user', 'comments'])->latest()->paginate(10);
    }

    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'title' => 'required|string',
                'content' => 'required|string',
                // 'tags' => 'array', // Optional
                // 'tags.*' => 'integer|exists:tags,id',
            ]);


            $post = Auth::guard('api')->user()->posts()->create($validated);


            // // Attach tags to the post
            // if (!empty($validated['tags'])) {
            //     $post->tags()->attach($validated['tags']);
            // }

            return response()->json($post );
            // return response()->json($post->load(['tags', 'user', 'comments'])); 
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        };
    }
}
