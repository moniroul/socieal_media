<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

    public function index(Request $request)
    {

        $validated = $request->validate([
            'posts_per_page' => 'integer|min:1|max:50',      // Limit posts per page
            'posts_page' => 'integer|min:1',                // Posts page
            'comments_per_page' => 'integer|min:1|max:50',  // Limit comments per page
        ]);

        // Set default pagination parameters
        $postsPerPage = $validated['posts_per_page'] ?? 5;
        $postsPage = $validated['posts_page'] ?? 1;
        $commentsPerPage = $validated['comments_per_page'] ?? 50;

        // Get paginated posts
        $posts = Post::with(['comments'  => function ($query) use ($commentsPerPage) {
            $query->with('user')->latest()->paginate($commentsPerPage);
        }])->with(['user', 'tags'])
            ->latest()->paginate($postsPerPage, ['*'], 'posts_page', $postsPage);
        // Return the response
        return response()->json($posts);
    }


    //  // Validate pagination inputs
    //  $validated = $request->validate([
    //     'posts_per_page' => 'integer|min:1|max:50',      // Limit posts per page
    //     'posts_page' => 'integer|min:1',                // Posts page
    //     'comments_per_page' => 'integer|min:1|max:50',  // Limit comments per page
    // ]);

    // // Set default pagination parameters
    // $postsPerPage = $validated['posts_per_page'] ?? 5;
    // $postsPage = $validated['posts_page'] ?? 1;
    // $commentsPerPage = $validated['comments_per_page'] ?? 2;

    // // Get paginated posts
    // $posts = Post::with(['user'])
    //     ->with(['comments' => function ($query) use ($commentsPerPage) {
    //         $query->with('user')->latest()->paginate($commentsPerPage);
    //     }])
    //     ->latest()->paginate($postsPerPage, ['*'], 'posts_page', $postsPage);


    // // Return the response
    // return response()->json($posts);


    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'title' => 'required|string',
                'content' => 'required|string',
                'tags' => 'array', // Optional
                'tags.*' => 'integer|exists:tags,id',
            ]);


            $post = Auth::guard('api')->user()->posts()->create($validated);


            // // Attach tags to the post
            if (!empty($validated['tags'])) {
                $post->tags()->attach($validated['tags']);
            }

            // return response()->json($post);
            return response()->json($post->load(['tags', 'user', 'comments']));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        };
    }
}
