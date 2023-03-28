<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Throwable;

class LikeController extends Controller
{
    //like post
    public function like(Request $request, $id)
    {
        // $data = $request->all();
        $post = Post::find($id);
        if ($post) {
            $data['post_id'] = $id;
            $data['user_id'] = auth()->user()->id;

            try {
                $like = Like::create($data);
                return response()->json(['message' => 'like successful', 'detail' => $like]);
            } catch (Throwable $e) {
                report($e);
                return response()->json(['message' => 'already liked']);
            }
        }
        return response()->json(['message' => 'post not found']);
    }

    //unlike post
    public function unlike($id)
    {
        $post = Post::find($id);
        if ($post) {
            $user_id = auth()->user()->id;
            //find like that belong to current user
            $like = Like::where('post_id', $id)->where('user_id', $user_id);
            $like->delete();

            return response()->json(['message' => 'like remove successful']);
        }
    }

    public function getPostLike($id)
    {
        $post = Post::find($id);
        if ($post) {
            //get likes by post id
            $likes = Post::with('likes.user')->where('id', $id)->get();

            return response()->json(['post' => $likes]);
        }
    }
}
