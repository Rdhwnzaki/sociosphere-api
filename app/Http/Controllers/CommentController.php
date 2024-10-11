<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function createComment(Request $request, $postId)
    {
        $request->validate([
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $post = Post::findOrFail($postId);

        $commentData = [
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'description' => $request->description
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/comments', 'public');
            $commentData['image'] = $path;
        }

        $comment = Comment::create($commentData);

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => $comment
        ], 201);
    }

    public function updateComment(Request $request, $commentId)
    {
        $request->validate([
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $comment = Comment::findOrFail($commentId);

        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($request->filled('description')) {
            $comment->description = $request->description;
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/comments', 'public');
            $comment->image = $path;
        }

        $comment->save();

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment
        ]);
    }
}
