<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;

class ProfileController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        $posts = \App\Models\Post::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $numPosts = \App\Models\Post::where('user_id', $user->id)->count();

        return view('profile', [
            'user' => $user,
            'profile' => $profile,
            'posts' => $posts,
            'numPosts' => $numPosts
        ]);
    }

    public function create()
    {
        return view('createProfile', [
            'profile' => null
        ]);
    }

    public function edit()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        return view('editProfile', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    public function postEdit()
    {
        $data = request()->validate([
            'description' => 'required',
            'profilepic' =>  'image',
        ]);

        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        $profile->description = request('description');
        
        if (request()->has('profilepic')){
            $imagePath = request('profilepic')->store('uploads', 'public');
            $profile->image = $imagePath;
        }
        
        $updated = $profile->save();
        if ($updated) {
            return redirect('/profile');
        }
    }

    public function postCreate()
    {
        $data = request()->validate([
            'description' => 'required',
            'profilepic' => ['required', 'image'],
        ]);

        // Create a new profile
        $user = Auth::user();
        $profile = new Profile();
        $profile->user_id = $user->id;

        // Save description
        $profile->description = request('description');

        // Save the new profile pic... if there is one in the request()!
        if (request()->has('profilepic')) {
            $imagePath = request('profilepic')->store('uploads', 'public');
            $profile->image = $imagePath;
        }

        // Now, save it all into the database
        $updated = $profile->save();
        if ($updated) {
            return redirect('/profile');
        }
    }
}
