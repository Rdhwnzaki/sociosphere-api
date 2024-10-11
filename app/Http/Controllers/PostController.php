<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function getAllPosts()
    {
        $posts = Post::with('user')->get();

        return response()->json([
            'message' => "All posts retrieved successfully",
            'posts' => $posts
        ], 200);
    }

    public function getPostsByLoggedInUser()
    {
        $user = Auth::User();

        $posts = Post::where('user_id', $user->id)->with('user')->get();

        return response()->json([
            'message' => "Posts retreived successfully",
            'posts' => $posts
        ]);
    }

    public function likePost($postId)
    {
        $post = Post::find($postId);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }

        $post->likes += 1;
        $post->save();

        return response()->json([
            'message' => 'Post liked successfully',
            'likes' => $post->likes
        ]);
    }

    public function createPost(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        $post = new Post();
        $post->user_id = $user->id;
        $post->description = $request->description;
        $post->likes = 0;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $post->image = $path;
        }

        $post->save();

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }

    public function updatePost(Request $request, $postId)
    {
        $request->validate([
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $post = Post::find($postId);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($request->has('description')) {
            $post->description = $request->description;
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $post->image = $path;
        }

        $post->save();

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ]);
    }

    public function deletePost($postId)
    {
        $post = Post::find($postId);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
