<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        //get current user id
        $author_id = auth()->user()->id;

        //get post that belong to this user
        $posts = User::with('posts')->find($author_id);


        return response()->json(['user' => $posts]);
    }

    public function post(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'post_image' => 'required|image'
        ]);

        if ($request->hasFile('post_image')) {
            $image = $request->file('post_image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath =  public_path('/post_images');
            $image->move($destinationPath, $name);
            $data['post_image'] = '/post_images/' . $name;
        }

        if ($request->has('description')) {
            $data['description'] = $request->get('description');
        }

        $author_id =  auth()->user()->id;
        $data['user_id'] = $author_id;

        $post = Post::create($data);

        return response()->json($post, 200);
    }
}
