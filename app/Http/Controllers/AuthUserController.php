<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthUserController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        //if request data contain file('image')
        if ($request->hasFile('image')) {
            //declare variable called image to take value from 'image' key that contain file
            $image = $request->file('image');

            //get extension from image file and concate it with time() function
            $name = time() . '.' . $image->getClientOriginalExtension();

            //create destination path when image should store
            $destinationPath = public_path('/image');

            //move image file to destination path and rename it
            $image->move($destinationPath, $name);

            //add string variable called '$name' to date['image']
            $data['image'] = $name;
        }

        //encrypt password
        $data['password'] = bcrypt($request->password);

        //create new user with '$data' that passed from request
        $user = User::create($data);

        //generate access token for user
        $token = $user->createToken('authToken')->accessToken;

        //return json back with status 200
        return response()->json(['user' => $user, 'access_token' => $token], 200);
    }
}
