<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AuthUserController extends Controller
{
    //register
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

    //login user
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        //if the given credentials is correct
        if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            //get user
            $user = auth()->user();

            //create token for current user
            $token = $user->createToken('authToken')->accessToken;

            //response back
            return Response()->json(['message' => 'Login successful', 'user' => $user, 'access_token' => $token]);
        }

        //if it fail to find the matching crediential
        return response(['message' => 'Incorrect infomation!']);
    }

    //logout user
    public function  logout(Request $request)
    {
        $user = auth()->user()->token();
        $user->revoke();
        return response(['success' => 'Logout Successful']);
    }

    //update user
    public function update(Request $request, $id)
    {
        //find user that have the same id
        $user = User::find($id);

        //if it fount user
        if ($user) {
            $data = $request->validate([
                'name' => 'string',
                'email' => 'string|email|max:255',
                'password' => 'string|min:6|confirmed',
            ]);

            //if the given data has file
            if ($request->hasFile('image')) {
                //get file
                $image = $request->file('image');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath =  public_path('/image');
                $image->move($destinationPath, $name);
                $data['image'] = $name;

                //delete old image from current user
                $oldImage = public_path('/image/') . $user->image;
                if (file_exists($oldImage)) {
                    File::delete($oldImage);
                }
            }
            if ($request->has('password')) {
                $data['password'] = bcrypt($request->password);
            }
            $user->update($data);
            return response(['message' => 'Update successful', 'user' => $user], 200);
        }

        //if it fail to find user
        return response(['message' => 'User not found'], 404);
    }

    //get current user
    public function user()
    {
        $user = auth()->user();
        return response()->json(['user' => $user], 200);
    }

    //get all users
    public function all()
    {
        $users = User::all();
        $count = $users->count();
        return response()->json(['count' => $count, 'users' => $users]);
    }
}
