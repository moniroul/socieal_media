<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string',
            ]);
            $user = Auth::guard('api')->user();
            $comment = Comment::create([
                'content' => $validated['content'],
                'post_id' => $postId,
                'user_id' => $user->id
            ]);

            return response()->json($comment);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        };
    }
}
