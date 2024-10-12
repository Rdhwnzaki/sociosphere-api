<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function createComment(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = new Comment();
        $comment->user_id = $request->user()->id;
        $comment->post_id = $request->post_id;
        $comment->description = $request->description;
        $comment->parent_id = $request->parent_id;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $comment->image = $path;
        }

        $comment->save();

        return response()->json(['message' => 'Comment created successfully', 'comment' => $comment], 201);
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

    public function replyToComment(Request $request, $commentId)
    {
        $parentComment = Comment::findOrFail($commentId);

        $request->validate([
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $reply = new Comment();
        $reply->user_id = $request->user()->id;
        $reply->post_id = $parentComment->post_id;
        $reply->description = $request->description;
        $reply->parent_id = $parentComment->id;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $reply->image = $path;
        }

        $reply->save();

        return response()->json(['message' => 'Reply added successfully', 'reply' => $reply], 201);
    }
}
