<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function comment(Request $request, $id)
    {
        if (!$request->has('comment') && !$request->hasFile('comment_photo')) {
            $request->validate(['comment' => 'required|string']);
        }
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;

        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'post not found']);
        }
        $data['post_id'] = $id;

        if ($request->hasFile('comment_photo')) {
            $image = $request->file('comment_photo');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path('/comment_photo');
            $image->move($path, $name);
            $data['comment_photo'] = '/comment_photo/' . $name;
        }

        $comment = Comment::create($data);

        return response()->json([
            'comment_response' => $comment
        ], 200);
    }

    public function removeComment($pid, $cid)
    {
        $post = Post::find($pid);
        if (!$post) {
            return response()->json(['message' => 'post not found']);
        }

        $comment = Comment::find($cid);
        if (!$comment) {
            return response()->json(['message' => 'comment not found']);
        }

        $user_id = $comment->user_id;
        if ($user_id == auth()->user()->id) {
            $comment->delete();

            return response()->json(['message' => 'comment deleted successful']);
        }
        return response()->json(['message' => 'only owner of this comment can delete']);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if ($comment) {
            //prevent user to update post_id & user_id from their comment
            $newComment = $request->except(['post_id', 'user_id']);
            $comment->update($newComment);

            return response()->json(['message' => 'updated successful', 'new_comment' => $newComment]);
        }

        return response()->json(['message' => 'comment could not found']);
    }

    public function getComment($pid)
    {
        $post = Post::find($pid);
        if ($post) {
            $comments = Post::with('comments.user')->where('id', $pid)->get();

            return response()->json(['comment_onPost' => $comments]);
        }
        return response()->json(['message' => 'post not found'], 404);
    }
}
